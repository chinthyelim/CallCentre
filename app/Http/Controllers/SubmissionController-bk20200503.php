<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use PDO;
use App\models\Submissions;

require_once dirname(__FILE__) . '/../../../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

class SubmissionController extends Controller
{
  protected $errors = array();

  public function __construct()
  {
    $this->middleware('auth');
  }

  // Class destructor
  public function __destruct() {
    if (count($this->errors)) {
      $this->printCustomErrorLog();
    }
  }

  /**
   * printCustomErrorLog
   * @access  protected
   * @desc  Print custom error log file.
   */
  protected function printCustomErrorLog()
  {
    $h = fopen(__DIR__ . '/../errorLog/errorlog'. date('Y-m-d') . '.txt', 'a'); // Open a file
    fwrite($h, PHP_EOL);
    fwrite($h, print_r($this->errors, TRUE) . PHP_EOL); // Write output
    fwrite($h, 'Occurred on: ' . date('Y-m-d H:i:s') . PHP_EOL);
    fclose($h); // Close file
  }

  public function index(Request $request)
  {
    // SELECT JSON_EXTRACT(data, '$[0].QuestionnaireCategory') FROM `submissions` WHERE JSON_EXTRACT(data, '$[0].QuestionnaireCategory') LIKE '%Tele%'
    // select * from `submissions` where json_contains(`data`, '{"Answer" : "no"}')
    return view('submissions');
  }

  public function insert(Request $request)
  {
    try {
      // Check user's access permission
      // $submissions = DB::table('submissions')->get();
      // $this->authorize('create', $submissions);
      //SET GLOBAL time_zone = '+10:00';
      //SET SESSION time_zone = '+10:00';
      $ticket_number = '';

      $array_submission_data = $request->post('submission_data');
      foreach ($array_submission_data as $index => $fields) {
        if ( ! empty($fields['TicketNumber'])) {
          $ticket_number = $fields['TicketNumber'];
          break;
        }
      }
      // Validate if ticket number already existed?
      $results = DB::table('submissions')->where('ticketnumber', $ticket_number)->first();
      if ($results !== null) {
        return ['success' => FALSE, 'return_id' => $ticket_number];
        exit(0);
      }

      Submissions::create([
        'ticketnumber' => $ticket_number,
        'data' => json_encode($request->post('submission_data')),
        'userid' => \Auth::id()
      ]);
    } catch (Exception $e) {
      $this->errors[] = date("l jS \of F Y h:i:s A") . ' SubmissionController - Caught exception: ' .  $e->getMessage() . "\n";
      return ['success' => FALSE];
      exit(0);
    }

    //DB::getPdo()->lastInsertId()
    return ['success' => TRUE, 'return_id' => $ticket_number];
  }

  public function list(Request $request)
  {
        //composer require illuminate/events
        //$dispatcher = new \Illuminate\Events\Dispatcher();
        //$db = $this->get('db');
        //$db->setEventDispatcher($dispatcher);
        //$dispatcher->listen(\Illuminate\Database\Events\StatementPrepared::class, function ($event) {
        //    $event->statement->setFetchMode(PDO::FETCH_ASSOC);
        //});

    // get original model
    //$fetch_mode = DB::getFetchMode();
    // set mode to custom
    //DB::setFetchMode(PDO::FETCH_ASSOC);
    //date_from: date_from, date_to: date_to, questionnaire_cat_id: questionnaire_cat_id, user_contact_name: user_contact_name, current_page_number: (this.current_page_number / this.ajax_rotate_page_gap), rows_per_ajax_items: this.rows_per_ajax_items
    //WHERE 1 = 1 AND id BETWEEN 1 + (0) AND 11 + (0)
    $sql_from = 'FROM submissions a '
                . 'JOIN users b ON a.userid = b.id ';
    $sql_where = 'WHERE 1 = 1 ';
    if (strlen($request->input('date_from')) && strlen($request->input('date_to'))) {
      $sql_where .= "AND DATE(a.created_at) BETWEEN CAST('" . $request->input('date_from') . "' AS DATE) AND CAST('" . $request->input('date_to') . "' AS DATE) ";
    }
    if (strlen($request->input('questionnaire_cat_id')) && $request->input('questionnaire_cat_id') != '0') {
      $sql_where .= "AND JSON_EXTRACT(a.data, '$[0].QuestionnaireID') = '" . $request->input('questionnaire_cat_id') . "'";
    }
    if (strlen($request->input('user_contact_name'))) {
      $sql_where .= "AND b.name LIKE '%" . $request->input('user_contact_name') . "%' ";
    }
    if (strlen($request->input('ticket_number'))) {
      $sql_where .= "AND a.ticketnumber LIKE '%" . $request->input('ticket_number') . "%' ";
    }

    /*
    if ($request->input('is_csv') === null) {
      $sql = 'SELECT COUNT(*) total '
              . $sql_from
              . $sql_where;
      $count = DB::select($sql);
      $count = array_map(function($value){
        return (array) $value;
      }, $count);
    }
    */
    $sql = "SELECT a.id, b.name contactname, "
              . "REPLACE(JSON_EXTRACT(a.data, '$[0].QuestionnaireID'), '" . '"' . "', '') questionnaireid, "
              . "CONCAT(REPLACE(JSON_EXTRACT(a.data, '$[0].QuestionnaireCategory'), '" . '"' . "', ''), ' - ', REPLACE(JSON_EXTRACT(a.data, '$[0].QuestionnaireName'), '" . '"' . "', '')) questionnaire,  "
              . "a.created_at createdat, ticketnumber "
              //. "CASE WHEN status <> 0 THEN 'Processing' ELSE 'Pending' END status "
            . $sql_from
            . $sql_where
            . ' ORDER BY a.id DESC';
    $results = DB::select($sql);

    if ($request->input('is_csv') === null) {
      $collect = collect($results);
      $pagination_results = new LengthAwarePaginator(
                           $collect->forPage(((int)$request->input('current_page_number') + 1), (int)$request->input('rows_per_ajax_items')),
                           $collect->count(),
                           (int)$request->input('rows_per_ajax_items'),
                           ((int)$request->input('current_page_number') + 1)
                         );
      // restore mode the original
      //DB::setFetchMode($fetch_mode);
      /*
      */
      foreach ($pagination_results as $index => $result) {
        $array = (array) $result;
        $array['createdat'] = date('h:i:sa l d-m-Y', strtotime($array['createdat']));
        $arrays[] = $array;
      }
      $arrays[0]['total_records'] = $collect->count();
    } else {
      $results = array_map(function($value) {
        $array = (array) $value;
        $array['createdat'] = date('h:i:sa l d-m-Y', strtotime($array['createdat']));
        return $array;
      }, $results);

      $filename = 'questionnaire_submitted_list_' . \Auth::id() . '_' . date('dmYHisu') . '.csv';
      $file_path = __DIR__  . '/../../../public/download/' . $filename;
      //$file_path = __DIR__  . '/../../../../../callcentre/downloadcsv/' . $filename;
      //$file_path = __DIR__  . '/../../../../public_html/callcentre/downloadcsv/' . $filename;
//die($file_path);
      // create a file pointer connected to the output stream
      $output = fopen($file_path, 'w');
      // output the column headings
      fwrite($output, '"' . implode('","', array('RowID', 'Questionnaire', 'Submitted By', 'Submitted On', 'Ticket Number')) . '"' . chr(13) );
      foreach($results as $row) {
        $rowCSV = array(
          '"' . $row['id'] . '"',
          '"' .$row['questionnaire'] . '"',
          '"' .$row['contactname'] . '"',
          '"' .$row['createdat'] . '"',
          '"' .$row['ticketnumber'] . '"',
        );
        fwrite($output, implode(',', $rowCSV) . chr(13));
      }
      fclose($output);

      header('Content-Type: text/csv; charset=utf-8');
      header('Content-Disposition: attachment; filename=' . $filename);

      echo file_get_contents($file_path);

      // Remove the files from file system
      flush();
      //unlink($file_path);
    }
    return $arrays;
  }

  public function auto(Request $request)
  {
    $results = array();
    switch ($request->post('search_type')) {
      case 'user_contact_names':
        $results = DB::select(
          "
          SELECT a.id value, a.name label
          FROM users a
          WHERE a.name LIKE '%" . $request->post('search_string') . "%'
          ORDER BY a.name
          LIMIT 10
          "
        );
        break;
      case 'ticket_numbers':
        $results = DB::select(
          "
          SELECT a.ticketnumber value, a.ticketnumber label
          FROM submissions a
          WHERE a.ticketnumber LIKE '%" . $request->post('search_string') . "%'
          ORDER BY a.ticketnumber
          LIMIT 10
          "
        );
    }
    $results = array_map(function($value){
      return (array) $value;
    }, $results);
    return $results;
  }

  public function loadSubmissionsPDF($ticket_number) {

    try {
      //ob_start();?>
      <style>
        .pageNumber {
          width: 100px;
          float: right;
          text-align: right;
          position: absolute;
          right: 0;
          bottom: 0;
        }
        .tbl {
          border-collapse: collapse;
        }
        .tbl th {
          border: 1px solid black;
          padding: 2px;
          background-color: black;
          font-weight: bold;
          color: #fff;
          vertical-align: top;
        }
        .tbl td {
          border: 1px solid black;
          padding: 2px;
        }
        .cat {
          text-align: left;
        }
        .hasNote td {
          border-bottom: 0 solid black;
        }
        .hasNote2 td {
          border-top: 0 solid black;
        }
        .no {
          width: 4%;
          vertical-align: top;
        }
        .question {
          vertical-align: top;
          width: 71%;
          font-weight: bold;
        }
        .answer {
          vertical-align: top;
          width: 15%;
        }
        .note {
          vertical-align: top;
          width: 100%;
        }
        .noteLabel {
          font-weight: bold;
          font-style: italic;
        }
        .hideReportClipboard {
          display: none;
        }
        .button {
          background-color: #0aa6d0;
          border: 2px solid #0aa6d0;
          color: whitesmoke;
          padding: 0.25em 0.5em;
          text-align: center;
          text-decoration: none;
          font-size: 1.1em;
          /* display: inline-block; */
          border-radius: 5px;
          box-shadow: 3px 3px 8px 0 #0aa6d0; /* h-offset v-offset blur spread color */
          cursor: pointer;
          margin-bottom: 20px;
        }
        .copyClipboard {
          width: 200px;
        }
      </style>
      <page orientation="p">
        <?php
          $results = DB::table('submissions')->where('ticketnumber', $ticket_number)->pluck('data');
          //$results = array_map(function($value){
          //  return (array) $value;
          //}, $results);
          ?><p>Below is the answered questionnaire for <b>ticket number: <?php echo $ticket_number;?></b>:</p>
          <button class="button copyClipboard" onclick="copyToClipboard()">Copy to Clipboard</button><?php
          $arrays = json_decode($results, TRUE);
          $array_questionnaire = json_decode($arrays[0], true);

//die('===>'.);
//print_r($array_questionnaire[0]);

//die($x[0]['Question']);
//print_r($x);
//die($arr['QuestionnaireName']);
          //$array_questionnaire = json_decode($results);
          //$array_questionnaire = json_decode($results->data);
          //$array_questionnaire = get_object_vars($array_submission);
          $s = '';
          $previous_category_name = '';
          foreach ($array_questionnaire as $index => $question) {
            if ($index == 0):?>
              <table class="tbl">
              <tr>
                <th>No.</th>
                <th>Question</th>
                <th>Answer</th>
              </tr>
            <?php endif;
            if ($previous_category_name != $question['QuestionnaireCategory'] . ' - ' . $question['QuestionnaireName']):?>
              <tr>
                <th colspan="3" class="cat">Questionnaire: <?php echo $question['QuestionnaireCategory'] . ' - ' . $question['QuestionnaireName'];
                  $s .= 'Questionnaire: ' . $question['QuestionnaireCategory'] . ' - ' . $question['QuestionnaireName'] . "\n\r";
                ?></th>
              </tr>
            <?php endif;
            $has_note = '';
            if ( ! empty($question['Note'])) {
              $has_note = ' class="hasNote"';
            }?>
            <tr<?php echo $has_note;?>>
              <td class="no"><?php echo ($index + 1); $s .= ($index + 1) . ". ";?>.</td>
              <td class="question"><?php echo $question['Question']; $s .= $question['Question'] . "\r";?></td>
              <td class="answer">
                <?php if ( ! empty($question['TicketNumber'])):
                  echo $question['TicketNumber'];
                  $s .= 'Answer: ' . $question['TicketNumber'] . "\r";
                else:
                  echo ucfirst($question['Answer']);
                  if ( ! empty($question['Answer'])) {
                    $s .= 'Answer: ' . ucfirst($question['Answer']) . "\r";
                  }
                endif;?>
              </td>
            </tr>
            <?php if ( ! empty($question['Note'])):?>
              <tr class="hasNote2">
                <td></td>
                <td class="note">
                  <span class="noteLabel">Note:</span><br/>
                  <?php echo $question['Note']; $s .= "Note:\r" . $question['Note'] . "\n\r";?>
                </td>
                <td></td>
              </tr>
            <?php else: $s .= "\n\r"?>

            <?php endif;
            $previous_category_name = $question['QuestionnaireCategory'] . ' - ' . $question['QuestionnaireName'];?>
            <?php if ($index == count($array_questionnaire) - 1):?>
              </table>
            <?php endif;
          }
        ?>
        <div id="report_clipboard" class="hideReportClipboard"><?php echo $s;?></div>
        <!--<div class="pageNumber">
          [[page_cu]]/[[page_nb]]
        </div>//-->
      </page>
      <script>
        const copyToClipboard = () => {
          const el = document.createElement('textarea');
          el.value = document.getElementById('report_clipboard').innerHTML;
          document.body.appendChild(el);
          el.select();
          document.execCommand('copy');
          document.body.removeChild(el);
        };
      </script>
      <?php //$content = ob_get_clean();

      //$html2pdf = new Html2Pdf('P', 'A4', 'fr');
      //$html2pdf->writeHTML($content);
      //$html2pdf->output('example01.pdf');
    } catch (Html2PdfException $e) {
      $html2pdf->clean();

      $formatter = new ExceptionFormatter($e);
      echo $formatter->getHtmlMessage();
    }
  }
}
