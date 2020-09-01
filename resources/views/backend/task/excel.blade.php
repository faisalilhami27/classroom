<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Nilai Tugas</title>
</head>
<body>
<table>
  <thead>
  <tr>
    <th colspan="5" style="text-align: center; font-weight: bold; font-size: 16px; font-family: 'Times New Roman'; text-transform: uppercase">Nilai Tugas {{ $task->title }}</th>
  </tr>
  <tr>
    <th colspan="5" style="text-align: center; font-weight: bold; font-size : 16px; font-family: 'Times New Roman'">{{ subjectName() }} {{ $task->studentClass->subject->name }}</th>
  </tr>
  <tr>
    <td></td>
  </tr>
  <tr>
    <th style="border: 1px solid black; font-weight: bold; font-size: 12px; font-family: 'Times New Roman', serif;" valign="middle" align="center">No</th>
    <th style="border: 1px solid black; font-weight: bold; font-size: 12px; font-family: 'Times New Roman', serif;" valign="middle" align="center">{{ sortIdentityNumber() }}</th>
    <th style="border: 1px solid black; font-weight: bold; font-size: 12px; font-family: 'Times New Roman', serif;" valign="middle" align="center">Nama</th>
    <th style="border: 1px solid black; font-weight: bold; font-size: 12px; font-family: 'Times New Roman', serif;" valign="middle" align="center">Kelas</th>
    <th style="border: 1px solid black; font-weight: bold; font-size: 12px; font-family: 'Times New Roman', serif;" valign="middle" align="center">Nilai</th>
  </tr>
  </thead>
  <tbody>
  @php $no = 1 @endphp
  @foreach($data as $item)
    <tr>
      <td valign="middle" align="center" style="border: 1px solid black;">{{ $no++ }}</td>
      <td valign="middle" align="center" style="border: 1px solid black; width: 20px;">{{ $item->sin }}</td>
      <td valign="middle" style=" border: 1px solid black; width: 40px;">{{ $item->name }}</td>
      <td valign="middle" align="center" style="border: 1px solid black; width: 50px;">{{ $item->class }}</td>
      <td valign="middle" align="center" style="border: 1px solid black;">{{ $item->score }}</td>
    </tr>
  @endforeach
  </tbody>
</table>
</body>
</html>
