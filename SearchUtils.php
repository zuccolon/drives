<?php
abstract class SearchUtils
{
    private static $db;

    public static function get_rides_between_areas($origin, $destination, $db_conn) {

        self::$db = $db_conn;

        // 1. Get origin locations cluster
        $cluster_origin = self::get_location_clusters($origin);

        // 2. Get destination locations cluster
        $cluster_destination = self::get_location_clusters($destination);

        // 3. Remove overlaps
        $cluster_origin         = array_diff($cluster_origin, $cluster_destination);
        $cluster_destination    = array_diff($cluster_destination, $cluster_origin);

        // 4. Get data
        $rides = self::get_rides_between_clusters($cluster_origin, $cluster_destination);

        // 5. Rank data
        $results = self::rank_results($rides, $origin, $destination);

        // 6. Return
        return $results;
    }

    private static function get_location_clusters($location) {
        $stmt = self::$db->prepare("SELECT destination
                                    FROM maps 
                                    WHERE origin = :origin");
        $stmt->bindParam(':origin', $location);
        $stmt->execute();

        $all_related_locations = [];
        $all_related_locations[] = $location;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $all_related_locations[] = $row['destination'];

        return $all_related_locations;
    }

    private static function get_rides_between_clusters($cluster_origin, $cluster_destination) {
        $stmt = self::$db->prepare("SELECT * 
                                    FROM rides
                                    WHERE origin IN ('" . implode("', '", $cluster_origin) . "')
                                     AND destination IN ('" . implode("', '", $cluster_destination) . "') 
                                     AND date >= NOW() 
                                    ORDER BY date ASC");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function rank_results($rides, $origin, $destination) {
        $results = [];

        while (count($rides) > 0) {
            foreach ($rides as $key => $ride) {
                if ($ride['origin'] == $origin && $ride['destination'] == $destination) {
                    $ride['rank'] = 0;
                    $results[] = $ride;
                    unset($rides[$key]);
                    break;
                } elseif($ride['origin'] == $origin || $ride['destination'] == $destination) {
                    $ride['rank'] = 1;
                    $results[] = $ride;
                    unset($rides[$key]);
                    break;
                }
                $ride['rank'] = 2;
                $results[] = $ride;
                unset($rides[$key]);
            }
        }

        return $results;
    }
}