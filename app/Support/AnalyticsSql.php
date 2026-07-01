<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Cross-driver SQL helpers for analytics (SQLite local, Postgres prod, MySQL
 * fallback) plus the distinct-visit / distinct-person counting that turns raw
 * event rows into real visits and real people.
 *
 * Shared by the per-manager AnalyticsController and the network-wide
 * NetworkAnalytics engine so the two can never diverge on how a "visit",
 * a "person", or a time bucket is computed.
 */
trait AnalyticsSql
{
    protected function dbDriver(): string
    {
        return DB::connection()->getDriverName();
    }

    /** SQL expression formatting created_at into a sortable period-bucket string. */
    protected function periodExpr(string $granularity): string
    {
        return match ($this->dbDriver()) {
            'sqlite' => match ($granularity) {
                'hour'  => "strftime('%Y-%m-%d %H:00', created_at)",
                'day'   => "strftime('%Y-%m-%d', created_at)",
                'week'  => "strftime('%Y-W%W', created_at)",
                'month' => "strftime('%Y-%m', created_at)",
            },
            'pgsql' => match ($granularity) {
                'hour'  => "to_char(created_at, 'YYYY-MM-DD HH24\":00\"')",
                'day'   => "to_char(created_at, 'YYYY-MM-DD')",
                'week'  => "to_char(created_at, 'IYYY\"-W\"IW')",
                'month' => "to_char(created_at, 'YYYY-MM')",
            },
            default => match ($granularity) { // mysql / mariadb
                'hour'  => "DATE_FORMAT(created_at, '%Y-%m-%d %H:00')",
                'day'   => "DATE_FORMAT(created_at, '%Y-%m-%d')",
                'week'  => "DATE_FORMAT(created_at, '%x-W%v')",
                'month' => "DATE_FORMAT(created_at, '%Y-%m')",
            },
        };
    }

    /** SQL expression extracting the hour-of-day (0..23) from created_at. */
    protected function hourExpr(): string
    {
        return match ($this->dbDriver()) {
            'sqlite' => "CAST(strftime('%H', created_at) AS INTEGER)",
            'pgsql'  => 'EXTRACT(HOUR FROM created_at)',
            default  => 'HOUR(created_at)',
        };
    }

    /**
     * SQL counting REAL visits, not raw events: dedupe by session_id so one
     * visitor firing ~10 events counts ONCE. Legacy rows with no session_id
     * fall back to their row id and still count once each.
     */
    protected function visitCountExpr(): string
    {
        $idAsText = in_array($this->dbDriver(), ['mysql', 'mariadb'], true)
            ? 'CAST(id AS CHAR)'
            : 'CAST(id AS TEXT)';
        return "COUNT(DISTINCT COALESCE(session_id, $idAsText))";
    }

    /** Run the distinct-visit count over a query. Clone the query first if reused. */
    protected function countVisits($query): int
    {
        $row = $query->selectRaw($this->visitCountExpr() . ' as aggregate')->first();
        return (int) ($row->aggregate ?? 0);
    }

    /**
     * SQL counting unique PEOPLE, not visits: distinct visitor_id (a localStorage
     * id that survives reloads + return visits). Falls back to session_id then
     * row id for legacy/no-storage rows.
     */
    protected function personCountExpr(): string
    {
        $idAsText = in_array($this->dbDriver(), ['mysql', 'mariadb'], true)
            ? 'CAST(id AS CHAR)'
            : 'CAST(id AS TEXT)';
        return "COUNT(DISTINCT COALESCE(visitor_id, session_id, $idAsText))";
    }

    /** Run the distinct-person count over a query. Clone the query first if reused. */
    protected function countPeople($query): int
    {
        $row = $query->selectRaw($this->personCountExpr() . ' as aggregate')->first();
        return (int) ($row->aggregate ?? 0);
    }
}
