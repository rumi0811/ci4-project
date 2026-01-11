<?php
class M_dashboard extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getInquiry()
    {
        return $this->db->query("
      SELECT DISTINCT t1.user_id, t2.name 
        FROM m_transaction_inquiry t1 
        INNER JOIN m_user t2 ON t1.user_id = t2.user_id
      WHERE t1.transaction_group_id <> 7
      ")->result_array();
    }

    public function getTotalInquiry()
    {
        return $this->db->query("
        SELECT t1.user_id, t1.date, t2.name, COUNT(*) total 
        FROM m_transaction_inquiry t1
        INNER JOIN m_users t2 ON t1.user_id = t2.user_id 
        WHERE t1.product_group_id <> 7
        GROUP BY t1.user_id
        ")->result_array();
    }

    public function getTotalByProductGroup()
    {
        return $this->db->query("
        SELECT t1.date,t4.name, COUNT(*) total 
        FROM m_transaction_inquiry t1
        INNER JOIN m_product_group t2 ON t1.product_group_id = t2.product_group_id
        WHERE t1.product_group_id <> 7
        GROUP BY t1.product_group_id
        ")->result_array();
    }

    public function lastTransaction($userType, $idUser)
    {
        if ($userType == 1) {
            return $this->db->query("SELECT t1.product_name, t2.amount, dateIndo(t2.date) date, t2.status, t2.refund_date 
          FROM m_product t1 INNER JOIN
          m_transaction t2 
          ON t1.product_group_id = t2.product_group_id
          WHERE t2.product_group_id IN (1,16) ORDER BY t2.date DESC LIMIT 4")->result_array();
        } elseif ($userType == 2) {
            return $this->db->query("select t1.product_name, t2.amount, dateIndo(t2.date) date, t2.status, t2.refund_date 
          from m_product t1 
          INNER JOIN m_transaction t2 ON t1.product_id = t2.product_id
          WHERE t2.product_group_id NOT IN(1,16) AND user_id=" . intval($idUser) . " ORDER BY t2.date DESC LIMIT 4")->result_array();
        }
    }

    public function notifWaitingConfirm($idAgent)
    {
        $this->db->query("
                SELECT COUNT(*) count FROM 
                m_payment_confirmation WHERE agent_id=$idAgent
        ")->result_array();
    }

    public function chartLine($userType, $userId)
    {
        if ($userType == 2) {
            $criteria = " WHERE user_id=$userId";
            $sumCriteria = "COUNT(*) AS revenue";
        } else {
            $criteria = " WHERE user_id = 0";
            $sumCriteria = "COUNT(*) AS revenue";
        }
        return $this->db->query("
              SELECT MONTH(a.date) as month,
              a.status,
              $sumCriteria
              FROM m_transaction a 
              $criteria
              GROUP BY MONTH(a.date)
              
      ")->result_array();
    }

    public function pieChartCompany()
    {
        return $this->db->query("
                SELECT name, COUNT(*) total 
                FROM m_user
                WHERE user_type_id = 2
                
        ")->result_array();
    }

    public function getOPeratorMasterDashboard()
    {
        return $this->db->query("
                select * from voucher_operator ORDER BY name 
        ")->result_array();
    }

    public function transaksiPulsa($idCompany, $idAgent = 0, $idPp = 0, $idLoket = 0, $date = '', $date2, $status = '')
    {
        // status 0 = success, status 1 = failed, 2 refund else ALL status
        if ($status == 0) {
            $criteriaAgent = ($idAgent == 0) ? " " : "AND t1.id_agent=$idAgent";
            $criteriaPp = ($idPp == 0) ? " " : "AND t1.id_pp=$idPp ";
            $criteriaLoket = ($idLoket == 0) ? " " : "AND t6.user_id=$idLoket ";
            if ($idLoket == 0) {
                $join = "";
            } else {
                $join = "INNER JOIN pp t5 ON t1.id_pp = t5.pp_id";
                $join .= " INNER JOIN pp_transaction t6 ON t1.id_transaction = t6.reference_id";
                $join .= " INNER JOIN users t7 ON t6.user_id = t7.id";
            }
            $criteriaDate = ($date2 == 0) ? "AND DATE_FORMAT(t1.date,'%Y-%m-%d') = '$date'" : "AND DATE_FORMAT(t1.date,'%Y-%m-%d') BETWEEN '$date' AND '$date2'";

            return $this->db->query("
                select t1.id_transaction, t3.name,t1.voucher_operator_id,
                t1.nominal_operator,COUNT(t1.nominal_operator) as total_transaksi
                FROM transaction t1
                
                INNER JOIN voucher_operator t3 ON t1.voucher_operator_id = t3.voucher_operator_id
    			$join
                WHERE  t1.transaction_type_group=7
    			$criteriaAgent
    			$criteriaPp
    			$criteriaLoket
    			$criteriaDate AND t1.status=0
                GROUP BY t1.voucher_operator_id ORDER BY total_transaksi DESC
                ")->result_array();
        } else if ($status == 1) {
            $criteriaAgent = ($idAgent == 0) ? " " : "AND t1.id_agent=$idAgent";
            $criteriaPp = ($idPp == 0) ? " " : "AND t1.id_pp=$idPp ";
            $criteriaLoket = ($idLoket == 0) ? " " : "AND t6.user_id=$idLoket ";
            if ($idLoket == 0) {
                $join = "";
            } else {
                $join = "INNER JOIN pp t5 ON t1.id_pp = t5.pp_id";
                $join .= " INNER JOIN pp_transaction t6 ON t1.id_transaction = t6.reference_id";
                $join .= " INNER JOIN users t7 ON t6.user_id = t7.id";
            }
            $criteriaDate = ($date2 == 0) ? "AND DATE_FORMAT(t1.date,'%Y-%m-%d') = '$date'" : "AND DATE_FORMAT(t1.date,'%Y-%m-%d') BETWEEN '$date' AND '$date2'";

            return $this->db->query("
                select t1.id_transaction, t3.name,t1.voucher_operator_id,
                t1.nominal_operator,COUNT(t1.nominal_operator) as total_transaksi
                FROM transaction t1
                
                INNER JOIN voucher_operator t3 ON t1.voucher_operator_id = t3.voucher_operator_id
    			$join
                WHERE  t1.transaction_type_group=7
    			$criteriaAgent
    			$criteriaPp
    			$criteriaLoket
    			$criteriaDate AND t1.status=1
                GROUP BY t1.voucher_operator_id ORDER BY total_transaksi DESC
                ")->result_array();
        } elseif ($status == 2) {
            $criteriaAgent = ($idAgent == 0) ? " " : "AND t1.id_agent=$idAgent";
            $criteriaPp = ($idPp == 0) ? " " : "AND t1.id_pp=$idPp ";
            $criteriaLoket = ($idLoket == 0) ? " " : "AND t6.user_id=$idLoket ";
            if ($idLoket == 0) {
                $join = "";
            } else {
                $join = "INNER JOIN pp t5 ON t1.id_pp = t5.pp_id";
                $join .= " INNER JOIN pp_transaction t6 ON t1.id_transaction = t6.reference_id";
                $join .= " INNER JOIN users t7 ON t6.user_id = t7.id";
            }
            $criteriaDate = ($date2 == 0) ? "AND DATE_FORMAT(t1.date,'%Y-%m-%d') = '$date'" : "AND DATE_FORMAT(t1.date,'%Y-%m-%d') BETWEEN '$date' AND '$date2'";

            return $this->db->query("
                select t1.id_transaction, t3.name,t1.voucher_operator_id,
                t1.nominal_operator,COUNT(t1.nominal_operator) as total_transaksi
                FROM transaction t1
                
                INNER JOIN voucher_operator t3 ON t1.voucher_operator_id = t3.voucher_operator_id
    			$join
                WHERE t1.transaction_type_group=7
    			$criteriaAgent
    			$criteriaPp
    			$criteriaLoket
    			$criteriaDate AND t1.status=2
                GROUP BY t1.voucher_operator_id ORDER BY total_transaksi DESC
                ")->result_array();
        } else {
            $criteriaAgent = ($idAgent == 0) ? " " : "AND t1.id_agent=$idAgent";
            $criteriaPp = ($idPp == 0) ? " " : "AND t1.id_pp=$idPp ";
            $criteriaLoket = ($idLoket == 0) ? " " : "AND t6.user_id=$idLoket ";
            if ($idLoket == 0) {
                $join = "";
            } else {
                $join = "INNER JOIN pp t5 ON t1.id_pp = t5.pp_id";
                $join .= " INNER JOIN pp_transaction t6 ON t1.id_transaction = t6.reference_id";
                $join .= " INNER JOIN users t7 ON t6.user_id = t7.id";
            }
            $criteriaDate = ($date2 == 0) ? "AND DATE_FORMAT(t1.date,'%Y-%m-%d') = '$date'" : "AND DATE_FORMAT(t1.date,'%Y-%m-%d') BETWEEN '$date' AND '$date2'";

            return $this->db->query("
                select t1.id_transaction, t3.name,t1.voucher_operator_id,
                t1.nominal_operator,COUNT(t1.nominal_operator) as total_transaksi
                FROM transaction t1
                
                INNER JOIN voucher_operator t3 ON t1.voucher_operator_id = t3.voucher_operator_id
    			$join
                WHERE t1.transaction_type_group=7
    			$criteriaAgent
    			$criteriaPp
    			$criteriaLoket
    			$criteriaDate 
                GROUP BY t1.voucher_operator_id ORDER BY total_transaksi DESC
                ")->result_array();
        }
    }

    public function transaksiPulsaByProduct($idCompany, $idAgent = 0, $idPp = 0, $idLoket = 0, $date = '', $date2, $status = '')
    {
        // status 0 = success, status 1 = failed, 2 refund else ALL status
        if ($status == 0) {
            $criteriaAgent = ($idAgent == 0) ? " " : "AND t1.id_agent=$idAgent";
            $criteriaPp = ($idPp == 0) ? " " : "AND t1.id_pp=$idPp ";
            $criteriaLoket = ($idLoket == 0) ? " " : "AND t6.user_id=$idLoket ";
            if ($idLoket == 0) {
                $join = "";
            } else {
                $join = "INNER JOIN pp t5 ON t1.id_pp = t5.pp_id";
                $join .= " INNER JOIN pp_transaction t6 ON t1.id_transaction = t6.reference_id";
                $join .= " INNER JOIN users t7 ON t6.user_id = t7.id";
            }
            $criteriaDate = ($date2 == 0) ? "AND DATE_FORMAT(t1.date,'%Y-%m-%d') = '$date'" : "AND DATE_FORMAT(t1.date,'%Y-%m-%d') BETWEEN '$date' AND '$date2'";

            return $this->db->query("
                SELECT t1.id_transaction,t1.transaction_type_group,
    			COUNT(t1.id_transaction) AS total_transaksi,t4.name product
    			FROM transaction t1
    			LEFT JOIN transaction t2 ON t1.ref_transaction_id = t2.id_transaction
    			INNER JOIN transaction_type t3 ON t1.transaction_type = t3.transaction_type_id
    			INNER JOIN transaction_type_group t4 ON t1.transaction_type_group = t4.transaction_group_id
    			$join
                WHERE t2.id_transaction IS NULL
    			$criteriaAgent
    			$criteriaPp
    			$criteriaLoket
    			$criteriaDate AND t1.status=0
                GROUP BY t1.transaction_type_group ORDER BY total_transaksi DESC
                ")->result_array();
        } elseif ($status == 1) {
            $criteriaAgent = ($idAgent == 0) ? " " : "AND t1.id_agent=$idAgent";
            $criteriaPp = ($idPp == 0) ? " " : "AND t1.id_pp=$idPp ";
            $criteriaLoket = ($idLoket == 0) ? " " : "AND t6.user_id=$idLoket ";
            if ($idLoket == 0) {
                $join = "";
            } else {
                $join = "INNER JOIN pp t5 ON t1.id_pp = t5.pp_id";
                $join .= " INNER JOIN pp_transaction t6 ON t1.id_transaction = t6.reference_id";
                $join .= " INNER JOIN users t7 ON t6.user_id = t7.id";
            }
            $criteriaDate = ($date2 == 0) ? "AND DATE_FORMAT(t1.date,'%Y-%m-%d') = '$date'" : "AND DATE_FORMAT(t1.date,'%Y-%m-%d') BETWEEN '$date' AND '$date2'";

            return $this->db->query("
                SELECT t1.id_transaction,t1.transaction_type_group,
    			COUNT(t1.id_transaction) AS total_transaksi,t4.name product
    			FROM transaction t1
    			LEFT JOIN transaction t2 ON t1.ref_transaction_id = t2.id_transaction
    			INNER JOIN transaction_type t3 ON t1.transaction_type = t3.transaction_type_id
    			INNER JOIN transaction_type_group t4 ON t1.transaction_type_group = t4.transaction_group_id
    			$join
                WHERE t2.id_transaction IS NULL
    			$criteriaAgent
    			$criteriaPp
    			$criteriaLoket
    			$criteriaDate AND t1.status=1
                GROUP BY t1.transaction_type_group ORDER BY total_transaksi DESC
                ")->result_array();
        } elseif ($status == 2) {
            $criteriaAgent = ($idAgent == 0) ? " " : "AND t1.id_agent=$idAgent";
            $criteriaPp = ($idPp == 0) ? " " : "AND t1.id_pp=$idPp ";
            $criteriaLoket = ($idLoket == 0) ? " " : "AND t6.user_id=$idLoket ";
            if ($idLoket == 0) {
                $join = "";
            } else {
                $join = "INNER JOIN pp t5 ON t1.id_pp = t5.pp_id";
                $join .= " INNER JOIN pp_transaction t6 ON t1.id_transaction = t6.reference_id";
                $join .= " INNER JOIN users t7 ON t6.user_id = t7.id";
            }
            $criteriaDate = ($date2 == 0) ? "AND DATE_FORMAT(t1.date,'%Y-%m-%d') = '$date'" : "AND DATE_FORMAT(t1.date,'%Y-%m-%d') BETWEEN '$date' AND '$date2'";

            return $this->db->query("
                SELECT t1.id_transaction,t1.transaction_type_group,
    			COUNT(t1.id_transaction) AS total_transaksi,t4.name product
    			FROM transaction t1
    			LEFT JOIN transaction t2 ON t1.ref_transaction_id = t2.id_transaction
    			INNER JOIN transaction_type t3 ON t1.transaction_type = t3.transaction_type_id
    			INNER JOIN transaction_type_group t4 ON t1.transaction_type_group = t4.transaction_group_id
    			$join
                WHERE t2.id_transaction IS NULL
    			$criteriaAgent
    			$criteriaPp
    			$criteriaLoket
    			$criteriaDate AND t1.status=2
                GROUP BY t1.transaction_type_group ORDER BY total_transaksi DESC
                ")->result_array();
        } else {
            $criteriaAgent = ($idAgent == 0) ? " " : "AND t1.id_agent=$idAgent";
            $criteriaPp = ($idPp == 0) ? " " : "AND t1.id_pp=$idPp ";
            $criteriaLoket = ($idLoket == 0) ? " " : "AND t6.user_id=$idLoket ";
            if ($idLoket == 0) {
                $join = "";
            } else {
                $join = "INNER JOIN pp t5 ON t1.id_pp = t5.pp_id";
                $join .= " INNER JOIN pp_transaction t6 ON t1.id_transaction = t6.reference_id";
                $join .= " INNER JOIN users t7 ON t6.user_id = t7.id";
            }
            $criteriaDate = ($date2 == 0) ? "AND DATE_FORMAT(t1.date,'%Y-%m-%d') = '$date'" : "AND DATE_FORMAT(t1.date,'%Y-%m-%d') BETWEEN '$date' AND '$date2'";

            return $this->db->query("
                SELECT t1.id_transaction,t1.transaction_type_group,
    			COUNT(t1.id_transaction) AS total_transaksi,t4.name product
    			FROM transaction t1
    			LEFT JOIN transaction t2 ON t1.ref_transaction_id = t2.id_transaction
    			INNER JOIN transaction_type t3 ON t1.transaction_type = t3.transaction_type_id
    			INNER JOIN transaction_type_group t4 ON t1.transaction_type_group = t4.transaction_group_id
    			$join
                WHERE t2.id_transaction IS NULL
    			$criteriaAgent
    			$criteriaPp
    			$criteriaLoket
    			$criteriaDate
                GROUP BY t1.transaction_type_group ORDER BY total_transaksi DESC
                ")->result_array();
        }
    }

    public function detailPulsaByProduct($id, $date, $date2, $id_company, $id_agent, $id_pp, $id_loket)
    {
        $criteriaAgent = ($id_agent == 0) ? " " : "AND t1.id_agent=$id_agent";
        $criteriaPp = ($id_pp == 0) ? " " : "AND t1.id_pp=$id_pp ";
        $criteriaLoket = ($id_loket == 0) ? " " : "AND t6.user_id=$id_loket ";
        if ($id_loket == 0) {
            $join = "";
        } else {
            $join = "INNER JOIN pp t5 ON t1.id_pp = t5.pp_id";
            $join .= " INNER JOIN pp_transaction t6 ON t1.id_transaction = t6.reference_id";
            $join .= " INNER JOIN users t7 ON t6.user_id = t7.id";
        }
        $criteriaDate = ($date2 == 0) ? "AND DATE_FORMAT(t1.date,'%Y-%m-%d') = '$date'" : "AND DATE_FORMAT(t1.date,'%Y-%m-%d') BETWEEN '$date' AND '$date2'";

        return $this->db->query("
            SELECT t1.id_transaction,t1.transaction_type_group,t3.transaction_name,
			COUNT(t1.id_transaction) AS total_transaksi,t4.name product
			FROM transaction t1
			INNER JOIN transaction_type t3 ON t1.transaction_type = t3.transaction_type_id
			INNER JOIN transaction_type_group t4 ON t1.transaction_type_group = t4.transaction_group_id
			$join
            WHERE t1.transaction_type_group = '$id'
			$criteriaAgent
			$criteriaPp
			$criteriaLoket
			$criteriaDate
            GROUP BY t3.transaction_type_id ORDER BY total_transaksi DESC
            ")->result_array();
    }

    public function detailPulsa($id, $date, $date2, $id_company, $id_agent, $id_pp, $id_loket)
    {
        $criteriaAgent = ($id_agent == 0) ? " " : "AND t1.id_agent=$id_agent";
        $criteriaPp = ($id_pp == 0) ? " " : "AND t1.id_pp=$id_pp ";
        $criteriaLoket = ($id_loket == 0) ? " " : "AND t5.user_id=$id_loket ";
        if ($id_loket == 0) {
            $join = "";
        } else {
            $join = "INNER JOIN pp t4 ON t1.id_pp = t4.pp_id";
            $join .= " INNER JOIN pp_transaction t5 ON t1.id_transaction = t5.reference_id";
            $join .= " INNER JOIN users t6 ON t5.user_id = t6.id";
        }
        $criteriaDate = ($date2 == 0) ? "AND DATE_FORMAT(t1.date,'%Y-%m-%d') = '$date'" : "AND DATE_FORMAT(t1.date,'%Y-%m-%d') BETWEEN '$date' AND '$date2'";

        return $this->db->query("
            select t1.id_transaction, t3.name,t1.voucher_operator_id,t1.status,dateIndo(t1.date) as date,
            t1.nominal_operator as nominal
            FROM transaction t1
            INNER JOIN voucher_operator t3 ON t1.voucher_operator_id = t3.voucher_operator_id
			$join
            WHERE t1.transaction_type_group=7 AND t3.voucher_operator_id=$id
			$criteriaAgent
			$criteriaPp
			$criteriaLoket
			$criteriaDate
            GROUP BY t1.id_transaction ORDER BY t1.id_transaction DESC
            ")->result_array();
    }

    public function transaksiAgent($idCompany, $idAgent = 0, $idPp = 0, $idLoket = 0, $date = '', $date2)
    {
        $criteriaAgent = ($idAgent == 0) ? " " : "AND t1.id_agent=$idAgent";
        $criteriaPp = ($idPp == 0) ? " " : "AND t1.id_pp=$idPp ";
        $groupPP = ($idAgent == 0) ? " " : ",t1.id_pp";
        $criteriaLoket = ($idLoket == 0) ? " " : "AND t6.user_id=$idLoket ";
        $groupLoket = ($idPp == 0) ? " " : ",t6.user_id";
        $criteriaDate = ($date2 == 0) ? "AND DATE_FORMAT(t1.date,'%Y-%m-%d') = '$date'" : "AND DATE_FORMAT(t1.date,'%Y-%m-%d') BETWEEN '$date' AND '$date2'";

        return $this->db->query("
            select t1.id_transaction,t4.agent_id, t4.name,t1.voucher_operator_id,
            COUNT(*) total,t1.status,t1.id_agent,t1.id_pp,t7.name PP,t8.name User,t5.name TG,t6.user_id
            FROM transaction t1
            LEFT JOIN transaction t2 ON t1.ref_transaction_id = t2.id_transaction
            INNER JOIN agent t4 ON t1.id_agent = t4.agent_id
            INNER JOIN transaction_type t3 ON t1.transaction_type = t3.transaction_type_id
			INNER JOIN transaction_type_group t5 ON t1.transaction_type_group = t5.transaction_group_id
			INNER JOIN pp_transaction t6 ON t6.reference_id = t1.id_transaction
			INNER JOIN pp t7 ON t7.pp_id = t6.pp_id
			INNER JOIN users t8 ON t8.id = t6.user_id
            WHERE t2.id_transaction IS NULL 
			$criteriaAgent
			$criteriaPp
			$criteriaLoket
			$criteriaDate
            GROUP BY t1.id_agent $groupPP $groupLoket ORDER BY total desc
            ")->result_array();
    }

    public function transaksiAgentDetail($id, $date, $date2)
    {
        $criteriaDate = ($date2 == 0) ? "AND DATE_FORMAT(t1.date,'%Y-%m-%d') = '$date'" : "AND DATE_FORMAT(t1.date,'%Y-%m-%d') BETWEEN '$date' AND '$date2'";

        return $this->db->query("
            select t1.id_transaction,t4.agent_id, t4.name,t1.voucher_operator_id,
            COUNT(*) total,t1.status,t1.id_agent,t1.id_pp,t7.name PP,t7.name User,t5.name TG
            FROM transaction t1
            LEFT JOIN transaction t2 ON t1.ref_transaction_id = t2.id_transaction
            INNER JOIN agent t4 ON t1.id_agent = t4.agent_id
            INNER JOIN transaction_type t3 ON t1.transaction_type = t3.transaction_type_id
			INNER JOIN transaction_type_group t5 ON t1.transaction_type_group = t5.transaction_group_id
			INNER JOIN pp_transaction t6 ON t6.reference_id = t1.id_transaction
			INNER JOIN pp t7 ON t7.pp_id = t6.pp_id
			INNER JOIN users t8 ON t8.id = t6.user_id
            WHERE t2.id_transaction IS NULL AND $id
			$criteriaDate
            GROUP BY t1.transaction_type_group ORDER BY total desc
            ")->result_array();
    }

    public function getRevenueCompany($idCompany, $idAgent = 0, $idPp = 0, $idLoket = 0, $date = '', $date2)
    {
        //$dateDefault = date("Y-m-d");
        $criteriaAgent = ($idAgent == 0) ? " " : "AND t1.id_agent=$idAgent";
        $criteriaPp = ($idPp == 0) ? " " : "AND t1.id_pp=$idPp ";
        $criteriaLoket = ($idLoket == 0) ? " " : "AND t5.user_id=$idLoket ";
        $criteriaDate = ($date2 == 0) ? "AND DATE_FORMAT(t1.date,'%Y-%m-%d') = '$date'" : "AND DATE_FORMAT(t1.date,'%Y-%m-%d') BETWEEN '$date' AND '$date2'";

        if ($idAgent <> 0 and $idPp == 0) {
            $sumCriteria = "SUM(t1.nett_pp-t1.nett_agent) as total";
            $join = "";
        } elseif ($idAgent <> 0 and $idPp <> 0 and $idLoket == 0) {
            $sumCriteria = "SUM(t1.nett_customer-t1.nett_pp) as total";
            $join = "INNER JOIN pp t5 ON t1.id_pp = t5.pp_id";
            $join .= " INNER JOIN pp_transaction t6 ON t1.id_transaction = t6.reference_id";
            $join .= " INNER JOIN users t7 ON t6.user_id = t7.id";
        } elseif ($idAgent <> 0 and $idPp <> 0 and $idLoket <> 0) {
            $sumCriteria = "SUM(t1.nett_customer-t1.nett_pp) as total";
            $join = " INNER JOIN pp_transaction t5 ON t1.id_transaction = t5.reference_id";
        } else {
            $join = "";
            $sumCriteria = "SUM(t1.nett_agent-t1.nett_company) as total";
        }

        return $this->db->query("
            select t4.name,t1.transaction_type_group,COUNT(*) as total_transaksi,t4.transaction_group_id,
	        $sumCriteria
            FROM transaction t1
	        INNER JOIN transaction_type t3 ON t1.transaction_type = t3.transaction_type_id
	        INNER JOIN transaction_type_group t4 ON t3.transaction_group_id = t4.transaction_group_id
            $join
            WHERE t1.status=0
            $criteriaAgent
            $criteriaPp
            $criteriaLoket 
            $criteriaDate
            GROUP BY t1.transaction_type_group ORDER BY total DESC
            ")->result_array();
    }

    public function getAgentRevenue($idCompany, $idAgent = 0, $idPp = 0, $idLoket = 0, $date = '', $userType = '', $date2)
    {
        $criteriaAgent = ($idAgent == 0) ? " " : "AND t1.id_agent=$idAgent";
        $criteriaPp = ($idPp == 0) ? " " : "AND t1.id_pp=$idPp ";
        $criteriaDate = ($date2 == 0) ? "AND DATE_FORMAT(t1.date,'%Y-%m-%d') = '$date'" : "AND DATE_FORMAT(t1.date,'%Y-%m-%d') BETWEEN '$date' AND '$date2'";

        if ($idAgent <> 0 and $idPp == 0) {
            $sumCriteria = "SUM(t1.nett_customer-t1.nett_pp) as total";
            $groupCriteria = "GROUP BY t1.id_pp";
            $join = "INNER JOIN pp t4 ON t1.id_pp = t4.pp_id";
            $fielId = "t4.pp_id,t4.agent_id,t4.name";
        } elseif ($idPp <> 0 and $idAgent <> 0) {
            $sumCriteria = "SUM(t1.nett_customer-t1.nett_pp) as total";
            $groupCriteria = "GROUP BY t1.id_pp";
            $join = "INNER JOIN pp t4 ON t1.id_pp = t4.pp_id";
            $join .= " INNER JOIN pp_transaction t5 ON t1.id_transaction = t5.reference_id";
            $join .= " INNER JOIN users t6 ON t5.user_id = t6.id";
            $fielId = "t6.id as user_id, t6.name,t4.pp_id";
        } else {
            $sumCriteria = "SUM(t1.nett_pp-t1.nett_agent) as total";
            $groupCriteria = "GROUP BY t1.id_agent";
            $join = "INNER JOIN agent t4 ON t1.id_agent = t4.agent_id";
            $fielId = "t4.agent_id,t4.name";
        }

        return $this->db->query("
            select t1.transaction_type_group,COUNT(*) as total_transaksi,$fielId,
	        $sumCriteria
            FROM transaction t1
	        INNER JOIN transaction_type t3 ON t1.transaction_type = t3.transaction_type_id
	        $join
            WHERE t1.status=0 
            $criteriaAgent 
            $criteriaPp 
            $criteriaDate  
            $groupCriteria ORDER BY total DESC
            ")->result_array();
    }

    function detailRevenue($id, $date, $date2, $idCompany, $idAgent = 0, $idPp = 0, $idLoket = 0)
    {
        $criteriaAgent = ($idAgent == 0) ? " " : "AND t1.id_agent=$idAgent";
        $criteriaPp = ($idPp == 0) ? " " : "AND t1.id_pp=$idPp ";
        $criteriaLoket = ($idLoket == 0) ? " " : "AND t5.user_id=$idLoket ";
        $criteriaDate = ($date2 == 0) ? "AND DATE_FORMAT(t1.date,'%Y-%m-%d') = '$date'" : "AND DATE_FORMAT(t1.date,'%Y-%m-%d') BETWEEN '$date' AND '$date2'";

        if ($idAgent <> 0 and $idPp == 0) {
            $sumCriteria = "SUM(t1.nett_pp-t1.nett_agent) ";
            $join = "";
        } elseif ($idAgent <> 0 and $idPp <> 0 and $idLoket == 0) {
            $sumCriteria = "SUM(t1.nett_customer-t1.nett_pp) ";
            $join = "INNER JOIN pp t5 ON t1.id_pp = t5.pp_id";
            $join .= " INNER JOIN pp_transaction t6 ON t1.id_transaction = t6.reference_id";
            $join .= " INNER JOIN users t7 ON t6.user_id = t7.id";
        } elseif ($idAgent <> 0 and $idPp <> 0 and $idLoket <> 0) {
            $sumCriteria = "SUM(t1.nett_customer-t1.nett_pp) ";
            $join = " INNER JOIN pp_transaction t5 ON t1.id_transaction = t5.reference_id";
        } else {
            $join = "";
            $sumCriteria = "SUM(t1.nett_agent-t1.nett_company) ";
        }

        return $this->db->query("
            select t4.name,t1.transaction_type_group,COUNT(*) as total_transaksi,t4.transaction_group_id,t3.transaction_name,
	        $sumCriteria as total
            FROM transaction t1
            INNER JOIN transaction_type t3 ON t1.transaction_type = t3.transaction_type_id
	        INNER JOIN transaction_type_group t4 ON t3.transaction_group_id = t4.transaction_group_id
            $join
            WHERE t1.status=0 AND t4.transaction_group_id = '$id'
            $criteriaAgent
            $criteriaPp
            $criteriaLoket 
            $criteriaDate
            GROUP BY t1.transaction_type_group,t1.transaction_type ORDER BY total DESC
            ")->result_array();
    }

    function detailRevenueAgent($id, $date, $date2)
    {
        $criteriaDate = ($date2 == 0) ? "AND DATE_FORMAT(t1.date,'%Y-%m-%d') = '$date'" : "AND DATE_FORMAT(t1.date,'%Y-%m-%d') BETWEEN '$date' AND '$date2'";

        if ($this->session->userdata('user_type') == 1) {
            return $this->db->query("
				select t1.transaction_type_group,t3.transaction_name,t1.nett_agent,t1.nett_company,t4.name agent,COUNT(*) as total_transaksi,SUM(t1.nett_pp-t1.nett_agent) AS total,t5.name product
				FROM transaction t1
				INNER JOIN agent t4 ON t1.id_agent = t4.agent_id
				INNER JOIN transaction_type t3 ON t1.transaction_type = t3.transaction_type_id
				INNER JOIN transaction_type_group t5 ON t5.transaction_group_id = t1.transaction_type_group
				WHERE t1.id_agent = '$id' AND t1.status=0
				$criteriaDate
				GROUP BY t1.transaction_type_group
				ORDER BY t3.transaction_name desc
				")->result_array();
        } elseif ($this->session->userdata('user_type') == 2) {
            return $this->db->query("
				select t1.transaction_type_group,t3.transaction_name,t1.nett_agent,t1.nett_company,t4.name agent,COUNT(*) as total_transaksi,SUM(t1.nett_customer-t1.nett_pp) as total,t5.name product
				FROM transaction t1
				INNER JOIN agent t4 ON t1.id_agent = t4.agent_id
				INNER JOIN transaction_type t3 ON t1.transaction_type = t3.transaction_type_id
				INNER JOIN transaction_type_group t5 ON t5.transaction_group_id = t1.transaction_type_group
				WHERE t1.id_pp = '$id' AND t1.status=0
				$criteriaDate
				GROUP BY t1.transaction_type_group
				ORDER BY t3.transaction_name desc
				")->result_array();
        } elseif ($this->session->userdata('user_type') == 3 || $this->session->userdata('user_type') == 5) {
            return $this->db->query("
				select t1.transaction_type_group,t3.transaction_name,t1.nett_agent,t1.nett_company,t4.name agent,COUNT(*) as total_transaksi,SUM(t1.nett_customer-t1.nett_pp) as total,t5.name product
				FROM transaction t1
				INNER JOIN agent t4 ON t1.id_agent = t4.agent_id
				INNER JOIN transaction_type t3 ON t1.transaction_type = t3.transaction_type_id
				INNER JOIN transaction_type_group t5 ON t5.transaction_group_id = t1.transaction_type_group
				INNER JOIN pp t6 ON t1.id_pp = t6.pp_id
				INNER JOIN pp_transaction t7 ON t1.id_transaction = t7.reference_id
				INNER JOIN users t8 ON t7.user_id = t8.id
				WHERE t1.status=0 AND t1.id_pp = '$id'
				$criteriaDate
				GROUP BY t1.transaction_type_group
				ORDER BY t3.transaction_name desc
				")->result_array();
        }
    }

    function getBillerBalance()
    {
        return $this->db->query("
        select biller_name, balance FROM m_biller 
        ORDER BY balance DESC
        ")->result_array();
    }
}
