<?php
    namespace Cron;

    require_once dirname(__DIR__).'/App/database/DBConnection.php';
    require_once dirname(__DIR__).'/App/Model/Subscriber.php';
    require_once dirname(__DIR__).'/App/Infra/GenericConsts.php';

    use database\DBConnection;
    use Model\Subscriber;
    use PDO;

    // error_log("OOOXI\n", 3, "/var/www/queue-processing/cron.log");

    require_once(dirname(__DIR__).'/env.php');

    $conn = new DBConnection();

    $subscriber = new Subscriber();

    $sql = 'SELECT * FROM jobs LIMIT 10;';
    $stmt = $conn->getDb()->query($sql);
    if($stmt) {
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (is_array($rows) && count($rows) > 0) {
            var_dump($rows);
            foreach ($rows as $row) {
                $subscriber->insertFromQueue($row['id'], json_decode(base64_decode($row['payload'])));
            }
        }
    }

    $conn->closeConnection();
?>