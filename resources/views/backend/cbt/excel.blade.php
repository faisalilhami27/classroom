<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Export Bank Soal</title>
</head>
<body>
<table>
  <thead>
      <tr>
        @if ($subjectId == 'all')
          <th colspan="11" style="text-align: center; font-weight: bold; font-size: 16px; font-family: 'Times New Roman'; text-transform:uppercase; border: 1px solid black">Seluruh Bank Soal</th>
        @else
          <th colspan="11" style="text-align: center; font-weight: bold; font-size: 16px; font-family: 'Times New Roman'; text-transform:uppercase; border: 1px solid black">Bank Soal {{ subjectName() }} {{ $subject->name }}</th>
        @endif
      </tr>
      <tr>
        <th valign="middle" align="center" style="border: 1px solid black">No</th>
        <th valign="middle" align="center" style="border: 1px solid black; width: 50px">{{ subjectName() }}</th>
        <th valign="middle" align="center" style="border: 1px solid black; width: 50px">{{ level() }}</th>
        <th valign="middle" align="center" style="border: 1px solid black; width: 100px">Pertanyaan</th>
        <th valign="middle" align="center" style="border: 1px solid black; width: 50px">Jawaban A</th>
        <th valign="middle" align="center" style="border: 1px solid black; width: 50px">Jawaban B</th>
        <th valign="middle" align="center" style="border: 1px solid black; width: 50px">Jawaban C</th>
        <th valign="middle" align="center" style="border: 1px solid black; width: 50px">Jawaban D</th>
        <th valign="middle" align="center" style="border: 1px solid black; width: 50px">Jawaban E</th>
        <th valign="middle" align="center" style="border: 1px solid black; width: 15px">Jawaban Benar</th>
        <th valign="middle" align="center" style="border: 1px solid black; width: 30px">File Tambahan</th>
      </tr>
  </thead>
  <tbody>
    @php $no = 1 @endphp
    @foreach($data as $d)
      <tr>
        <td valign="middle" align="center" style="border: 1px solid black">{{ $no++ }}</td>
        <td valign="middle" align="center" style="border: 1px solid black; width: 50px">{{ $d->subject }}</td>
        <td valign="middle" align="center" style="border: 1px solid black; width: 50px">{{ $d->level }}</td>
        <td valign="middle" align="center" style="border: 1px solid black; width: 100px">{{ $d->question_name }}</td>
        <td valign="middle" align="center" style="border: 1px solid black; width: 50px">{{ $d->answer0 }}</td>
        <td valign="middle" align="center" style="border: 1px solid black; width: 50px">{{ $d->answer1 }}</td>
        <td valign="middle" align="center" style="border: 1px solid black; width: 50px">{{ $d->answer2 }}</td>
        <td valign="middle" align="center" style="border: 1px solid black; width: 50px">{{ $d->answer3 }}</td>
        <td valign="middle" align="center" style="border: 1px solid black; width: 50px">{{ $d->answer4 }}</td>
        <td valign="middle" align="center" style="border: 1px solid black; width: 15px">{{ $d->answer }}</td>
        <td valign="middle" align="center" style="border: 1px solid black; width: 30px">{{ $d->document }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
</body>
</html>
