<?php

/**
 * Description of search
 *
 * @author NhatTieu
 */
class Search extends BaseModel
{

    /**
     * Search by SOLR
     * @param string $q
     * @param int $start
     * @param int $rows
     * @param string $sort
     * @return array|boolean
     */
    public function doSearch($q, $start, $rows, $sort = null)
    {
        $solr_url = 'http://42.117.5.183:8983/solr/wili/select?q=product_name:' . urlencode($q) . '&df=product_name&wt=json&start=' . $start . '&rows=' . $rows;
        $http = new Http_Common();
        $solr_result = $http->get($solr_url);

        if ($solr_result) {
            return json_decode($solr_result);
        } else {
            return false;
        }
    }

    public function getTopQuery($offset = 0, $limit = 20)
    {
        $stmt = $this->db->query("SELECT * FROM search WHERE totalresult > 0 ORDER BY count DESC LIMIT $offset, $limit");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertQuery($query, $totalresult)
    {
        $queryIsExists = $this->queryIsExist($query);
        if ($queryIsExists == false) {
            $stmt = $this->db->prepare("INSERT INTO search (query, count, totalresult) VALUES (:query, 1, :totalresult)");
            $stmt->execute(array(
                'query' => $query,
                'totalresult' => $totalresult
            ));
        } else {
            $gearman = GMJob::getInstance();
            $workload = array('query' => "UPDATE search SET count = count + 1 WHERE id = " . $queryIsExists);
            $gearman->doBackground('mysql_query_async', json_encode($workload));
            //$this->db->query("UPDATE search SET count = count + 1 WHERE id = " . $queryIsExists);
        }
    }

    public function queryIsExist($query)
    {
        $query = addslashes($query);
        $stmt = $this->db->query("SELECT *, COUNT(*) as num FROM search WHERE query LIKE '" . $query . "'");
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r['num'] > 0) {
            return $r['id'];
        } else {
            return false;
        }
    }

}
