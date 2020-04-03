<?php

namespace App\Statistics;

use Doctrine\DBAL\Driver\Connection;

class StatisticsAggregator
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function countTotalHelpers(): int
    {
        return $this->db->query('SELECT COUNT(*) FROM helpers')->fetchColumn();
    }

    public function countTotalHelpersByDay(): array
    {
        return $this->db->query('
            SELECT TO_CHAR(created_at, \'YYYY-mm-dd HH24\') AS day, COUNT(*) as nb
            FROM helpers
            WHERE created_at > current_date - interval \'7\' day
            GROUP BY day
            ORDER BY day ASC
        ')->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countMatchedHelpers(): int
    {
        return $this->db->query('SELECT COUNT(DISTINCT matched_with_id) FROM help_requests')->fetchColumn();
    }

    public function countTotalOwners(): int
    {
        return $this->db->query('SELECT COUNT(DISTINCT owner_uuid) FROM help_requests')->fetchColumn();
    }

    public function countTotalOwnersByDay(): array
    {
        return $this->db->query('
            SELECT TO_CHAR(created_at, \'YYYY-mm-dd HH24\') AS day, COUNT(DISTINCT owner_uuid) as nb
            FROM help_requests
            WHERE created_at > current_date - interval \'7\' day
            GROUP BY day
            ORDER BY day ASC
        ')->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countUnmatchedOwners(): int
    {
        return $this->db->query('SELECT COUNT(DISTINCT owner_uuid) FROM help_requests WHERE finished = false')->fetchColumn();
    }

    public function avgHelperAge(): int
    {
        return $this->db->query('SELECT AVG(age) FROM helpers')->fetchColumn();
    }

    public function countHelpersByDepartment(): array
    {
        return $this->db->query('
            SELECT SUBSTRING(zip_code FROM 1 FOR 2) AS department, COUNT(*) as nb 
            FROM helpers 
            GROUP BY department 
            ORDER BY nb DESC
        ')->fetchAll();
    }

    public function countGroceriesNeeds(): int
    {
        return $this->db->query('SELECT COUNT(*) FROM help_requests WHERE help_type = \'groceries\'')->fetchColumn();
    }

    public function countBabysitAggregatedNeeds(): int
    {
        return $this->db->query('SELECT COUNT(DISTINCT owner_uuid) FROM help_requests WHERE help_type = \'babysit\'')->fetchColumn();
    }

    public function countBabysitTotalNeeds(): int
    {
        return $this->db->query('SELECT COUNT(*) FROM help_requests WHERE help_type = \'babysit\'')->fetchColumn();
    }

    public function countOwnersByJobType(): array
    {
        return $this->db->query('SELECT job_type, COUNT(DISTINCT owner_uuid) as nb FROM help_requests GROUP BY job_type ORDER BY nb DESC')->fetchAll();
    }

    public function countOwnersByDepartment(): array
    {
        // Note: les 2 départements Corse (2A & 2B) sont considérés comme formant un unique département "20"
        return $this->db->query('
            SELECT SUBSTRING(zip_code FROM 1 FOR 2) AS department, COUNT(DISTINCT owner_uuid) as nb 
            FROM help_requests 
            GROUP BY department 
            ORDER BY nb DESC
        ')->fetchAll();
    }
}
