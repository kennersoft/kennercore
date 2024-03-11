<?php
/**
 * This file is part of EspoCRM and/or TreoCore, and/or KennerCore.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2020 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * TreoCore is EspoCRM-based Open Source application.
 * Copyright (C) 2017-2020 TreoLabs GmbH
 * Website: https://treolabs.com
 *
 * KennerCore is TreoCore-based Open Source application.
 * Copyright (C) 2020 Kenner Soft Service GmbH
 * Website: https://kennersoft.de
 *
 * KennerCore as well as TreoCore and EspoCRM is free software:
 * you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * KennerCore as well as TreoCore and EspoCRM is distributed in the hope that
 * it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of
 * the "KennerCore", "EspoCRM" and "TreoCore" words.
 */

declare(strict_types=1);

namespace Treo\Jobs;

use Espo\Core\Jobs\Base;
use Treo\Core\Container;
use Treo\Core\EventManager\Event;

/**
 * Class TreoCleanup
 *
 * @author r.ratsun <r.ratsun@treolabs.com>
 */
class TreoCleanup extends Base
{
    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $db;

    /**
     * @inheritDoc
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->date = (new \DateTime())->modify("-2 month")->format('Y-m-d');
        $this->db = $this->getConfig()->get('database')['dbname'];
    }

    /**
     * Run cron job
     *
     * @return bool
     */
    public function run(): bool
    {
        $this->cleanupJobs();
        $this->cleanupScheduledJobLog();
        $this->cleanupUniqueIds();
        $this->cleanupAuthLog();
        $this->cleanupActionHistory();
        $this->cleanupNotifications();
        $this->cleanupImportResults();
        $this->cleanupDeleted();
        $this->cleanupQueueManager();
        $this->cleanupAttachments();
        $this->cleanupDbSchema();

        // dispatch an event
        $this->getContainer()->get('eventManager')->dispatch('TreoCleanupJob', 'run', new Event());

        return true;
    }

    /**
     * Cleanup jobs
     */
    protected function cleanupJobs(): void
    {
        $this->exec("DELETE FROM job WHERE DATE(execute_time)<'{$this->date}' AND status IN ('Success','Failed')");
    }

    /**
     * Cleanup scheduled job logs
     */
    protected function cleanupScheduledJobLog(): void
    {
        $this->exec("DELETE FROM scheduled_job_log_record WHERE DATE(execution_time)<'{$this->date}'");
    }

    /**
     * Cleanup deleted
     */
    protected function cleanupDeleted(): void
    {
        $tables = $this->getEntityManager()->nativeQuery('show tables')->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            if ($table == 'attachment') {
                continue 1;
            }
            $columns = $this->getEntityManager()->nativeQuery("SHOW COLUMNS FROM {$this->db}.$table")->fetchAll(\PDO::FETCH_COLUMN);
            if (!in_array('deleted', $columns)) {
                continue 1;
            }
            if (!in_array('modified_at', $columns)) {
                $this->exec("DELETE FROM {$this->db}.$table WHERE deleted=1");
            } else {
                $this->exec("DELETE FROM {$this->db}.$table WHERE deleted=1 AND DATE(modified_at)<'{$this->date}'");
            }
        }
    }

    /**
     * Cleanup scheduled job logs
     */
    protected function cleanupQueueManager(): void
    {
        $this->exec("DELETE FROM queue_item WHERE DATE(modified_at)<'{$this->date}' AND status IN ('Success','Closed')");
    }

    /**
     * Cleanup unique ids
     */
    protected function cleanupUniqueIds(): void
    {
        $date = date('Y-m-d H:i:s');
        $this->exec("DELETE FROM `unique_id` WHERE terminate_at IS NOT NULL AND terminate_at<'$date'");
    }

    /**
     * Cleanup auth log
     */
    protected function cleanupAuthLog(): void
    {
        $this->exec("DELETE FROM `auth_log_record` WHERE DATE(created_at)<'{$this->date}'");
    }

    /**
     * Cleanup action history
     */
    protected function cleanupActionHistory(): void
    {
        $this->exec("DELETE FROM `action_history_record` WHERE DATE(created_at)<'{$this->date}'");
    }

    /**
     * Cleanup notifications
     */
    protected function cleanupNotifications(): void
    {
        $this->exec("DELETE FROM `notification` WHERE DATE(created_at)<'{$this->date}'");
    }

    /**
     * Cleanup ImportResults
     */
    protected function cleanupImportResults(): void
    {
        $date = (new \DateTime())->modify("-2 week")->format('Y-m-d');
        $this->exec("DELETE FROM `import_result_log` WHERE import_result_id in (SELECT id FROM import_result WHERE DATE(created_at)<'{$date}')");
        $this->exec("DELETE FROM `import_result` WHERE DATE(created_at)<'{$date}'");
    }

    /**
     * Cleanup attachments
     *
     * @todo will be developed soon
     */
    protected function cleanupAttachments(): void
    {
    }

    /**
     * Cleanup DB schema
     */
    protected function cleanupDbSchema(): void
    {
        try {
            $queries = $this->getContainer()->get('schema')->getDiffQueries();
        } catch (\Throwable $e) {
            $queries = [];
        }

        foreach ($queries as $query) {
            $this->exec($query);
        }
    }

    /**
     * @param string $sql
     */
    protected function exec(string $sql): void
    {
        try {
            $this->getEntityManager()->nativeQuery($sql);
        } catch (\PDOException $e) {
            $GLOBALS['log']->error('TreoCleanup: ' . $e->getMessage() . ' | ' . $sql);
        }
    }
}
