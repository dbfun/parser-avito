<?

class DataBaseMysql {
  private $dbId;
  const CONNECT_ERROR = 1,
        USE_DB_ERROR = 2,
        EXECUTE_ERROR = 3;
  public function __construct($host, $user, $password, $database) { if (!$this->dbId = @mysql_connect($host, $user, $password)) throw new Exception("MySQL: Unable to connect to database", self::CONNECT_ERROR); if (!mysql_select_db($database)) throw new Exception("MySQL: Unable to select database: ".$database, self::USE_DB_ERROR); }
  public function Query($sqlString) { if (!$resourseId =@mysql_query($sqlString, $this->dbId)) throw new Exception("MySQL: Unable to execute SQL: ".$sqlString.". Error (".mysql_errno()."): ".@mysql_error(), self::EXECUTE_ERROR); return $resourseId; }
  public function SelectValue($sqlString) { $resourseId = DataBaseMysql::Query($sqlString); $row = array(); $row = mysql_fetch_row($resourseId); @mysql_free_result($resourseId); return $row[0]; }
  public function SelectRow($sqlString) { $resourseId = DataBaseMysql::Query($sqlString); $row = array(); $row = mysql_fetch_assoc($resourseId); @mysql_free_result($resourseId); return $row; }
  public function SelectSet($sqlString, $idTable = '') { $resourseId = DataBaseMysql::Query($sqlString); $row = array(); while ($rowOne = mysql_fetch_assoc($resourseId)) { if ($idTable) $row[$rowOne[$idTable]] = $rowOne; else $row[] = $rowOne; } @mysql_free_result($resourseId); return $row; }
  public function SelectLastInsertId() { return @mysql_insert_id($this->dbId); }
  public function Destroy() { if (!@mysql_close($this->dbId)) throw new Exception("Cann't disconnect from database", self::CAN_NOT_DISCONNECT); }
  public function getDbId() { return $this->dbId; }
  public function getNumAffectedRows() { return mysql_affected_rows($this->dbId); }
  public function getDBO(stdClass $dbConfig) {
    $dbo = new DataBaseMysql($dbConfig->host, $dbConfig->user, $dbConfig->password, $dbConfig->table);
    $dbo->Query("SET NAMES UTF8");
    return $dbo;
    }
  }