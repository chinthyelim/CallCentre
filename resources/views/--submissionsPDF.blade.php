@extends('pdf')

@section('content')
<div class="container">
  @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
  @endif
  <div>
    <p>Below is the answered questionnaire for <b>ticket number: {{ $ticket_number }}</b></p>
    <div class="top">
      <div class="topLeft">
        <label>Submitted By: </label>
        <span><b>{{ $submitted_by }}</b></span>&nbsp;
        <br/>
        <label>on</label>&nbsp;
        <span><b>{{ $created_at }}</b></span>
      </div>
      <div class="topRight">
        <button class="button copyClipboard" onclick="copyToClipboard()">Copy to Clipboard</button>
      </div>
    </div>
    @php $s = '' @endphp
    @php $previous_category_name = '' @endphp
    @foreach ($array_questionnaire as $index => $question)
      @if ($index == 0)
        <table class="tbl">
        <tr>
          <th>No.</th>
          <th>Question</th>
          <th>Answer</th>
        </tr>
      @endif
      @if ($previous_category_name != $question['QuestionnaireID'])
        <tr>
          <th colspan="3" class="cat">Questionnaire: {{ $question['QuestionnaireCategory'] . ' - ' . $question['QuestionnaireName'] }}
            @php $s .= 'Questionnaire: ' . $question['QuestionnaireCategory'] . ' - ' . $question['QuestionnaireName'] . "\n\r" @endphp
          </th>
        </tr>
      @endif
      @php $has_note = '' @endphp
      @if ( ! empty($question['Note']))
        @php $has_note = ' class="hasNote" style="border-bottom: 0 solid black"' @endphp
      @endif
      @if (count($array_questionnaire) == ($index + 1))
        <tr>
          <td class="no">{{ ($index + 1) }}.
          @php $s .= ($index + 1) . ". " @endphp
          </td>
          <td class="question">{{ $question['Question'] }}
          @php $s .= $question['Question'] . "\r" @endphp
          </td>
          <td class="answer">
            @if ( ! empty($question['TicketNumber']))
              {{ $question['TicketNumber'] }}
              @php $s .= 'Answer: ' . $question['TicketNumber'] . "\r" @endphp
            @else
              {{ ucfirst($question['Answer']) }}
              @if ( ! empty($question['Answer']))
                @php $s .= 'Answer: ' . ucfirst($question['Answer']) . "\r" @endphp
              @endif
            @endif
          </td>
        </tr>
      @else
        <tr{{ $has_note }}>
          <td class="no" style="border-bottom: 0 solid black">{{ ($index + 1) }}.
          @php $s .= ($index + 1) . ". " @endphp
          </td>
          <td class="question" style="border-bottom: 0 solid black">{{ $question['Question'] }}
          @php $s .= $question['Question'] . "\r" @endphp
          </td>
          <td class="answer" style="border-bottom: 0 solid black">
            @if ( ! empty($question['TicketNumber']))
              {{ $question['TicketNumber'] }}
              @php $s .= 'Answer: ' . $question['TicketNumber'] . "\r" @endphp
            @else
              {{ ucfirst($question['Answer']) }}
              @if ( ! empty($question['Answer']))
                @php $s .= 'Answer: ' . ucfirst($question['Answer']) . "\r" @endphp
              @endif
            @endif
          </td>
        </tr>
      @endif
      @if ( ! empty($question['Note']))
        <tr class="hasNote2" style="border-top: 0 solid black">
          <td></td>
          <td class="note">
            <span class="noteLabel">Note:</span><br/>
            {{ $question['Note'] }}
            @php $s .= "Note:\r" . $question['Note'] . "\n\r" @endphp
          </td>
          <td></td>
        </tr>
      @else
        @php $s .= "\n\r" @endphp
      @endif
      @php $previous_category_name = $question['QuestionnaireID'] @endphp
      @if ($index == (count($array_questionnaire) - 1))
        </table>
      @endif
    @endforeach
    <div id="report_clipboard" class="hideReportClipboard">{{ $s }}</div>
  </div>
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
 </div>
@stop