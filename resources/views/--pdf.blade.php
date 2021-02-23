<!doctype html>
<html>
<head>
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
    <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.1.45/css/materialdesignicons.min.css" rel="stylesheet">//-->
<style>
  html {
    width: 690px;
    overflow-x: hidden;
  }
  .tbl {
    border-collapse: collapse;
    clear: both;
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
    border-top: 1px solid black;
    border-right: 1px solid black;
    border-bottom: 1px solid black;
    border-left: 1px solid black;
    padding: 2px;
  }
  .cat {
    text-align: left;
  }
  .tbl .hasNote, .tbl .hasNote td {
    border-bottom: 0px solid black !important;
  }
  .tbl .hasNote2, .tbl .hasNote2 td {
    border-top: 0px solid black !important;
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
    width: 180px;
    margin-right: 16px;
  }
  .top {
    width: 760px;
  }
  .topLeft {
    width: 490px;
    display: table-cell;
    vertical-align: top;
  }
  .topRight {
    width: 200px;
    display: table-cell;
    text-align: right;
  }
</style>
</head>
<body>
  @yield('content')
</body>
</html>