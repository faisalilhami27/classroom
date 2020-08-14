<?php

use App\Models\Navigation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $navigations = [
      [
        'id' => 1,
        'title' => 'Karyawan',
        'url' => 'employee.index',
        'icon' => 'icon icon-user',
        'order_num' => 1,
        'order_sub' => null,
        'parent_id' => 0,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 2,
        'title' => 'Siswa',
        'url' => 'student.index',
        'icon' => 'icon icon-user',
        'order_num' => 2,
        'order_sub' => null,
        'parent_id' => 0,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 3,
        'title' => 'Tahun Ajar',
        'url' => 'school.year.index',
        'icon' => 'icon icon-calendar',
        'order_num' => 3,
        'order_sub' => null,
        'parent_id' => 0,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 4,
        'title' => 'Penjurusan',
        'url' => '#',
        'icon' => 'icon icon-sitemap',
        'order_num' => 4,
        'order_sub' => null,
        'parent_id' => 0,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 5,
        'title' => 'Jurusan',
        'url' => 'major.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 1,
        'parent_id' => 4,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 6,
        'title' => 'Tingkat Pembelajaran',
        'url' => '#',
        'icon' => 'icon icon-sitemap',
        'order_num' => 6,
        'order_sub' => null,
        'parent_id' => 0,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 7,
        'title' => 'Tingkat Kelas',
        'url' => 'grade.level.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 1,
        'parent_id' => 6,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 8,
        'title' => 'Semester',
        'url' => 'semester.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 1,
        'parent_id' => 6,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 9,
        'title' => 'Kelola Pelajaran',
        'url' => '#',
        'icon' => 'icon icon-book',
        'order_num' => 9,
        'order_sub' => null,
        'parent_id' => 0,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 10,
        'title' => 'Mata Pelajaran',
        'url' => 'subject.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 1,
        'parent_id' => 9,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 11,
        'title' => 'Mata Kuliah',
        'url' => 'subject.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 1,
        'parent_id' => 9,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 12,
        'title' => 'KKM',
        'url' => 'minimal.criteria.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 2,
        'parent_id' => 9,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 13,
        'title' => 'Rombongan Belajar',
        'url' => '#',
        'icon' => 'icon icon-server',
        'order_num' => 13,
        'order_sub' => null,
        'parent_id' => null,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 14,
        'title' => 'Kelas',
        'url' => 'class.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 1,
        'parent_id' => 13,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 15,
        'title' => 'Pengaturan',
        'url' => '#',
        'icon' => 'icon icon-gears',
        'order_num' => 15,
        'order_sub' => null,
        'parent_id' => 0,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 16,
        'title' => 'Kelola User Karyawan',
        'url' => 'user.employee.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 1,
        'parent_id' => 15,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 17,
        'title' => 'Kelola User Siswa',
        'url' => 'user.student.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 2,
        'parent_id' => 15,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 18,
        'title' => 'Kelola Menu',
        'url' => 'navigation.index',
        'icon'=> null,
        'order_num' => null,
        'order_sub' => 3,
        'parent_id' => 15,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 19,
        'title' => 'Role',
        'url' => 'role.index',
        'icon'=> null,
        'order_num' => null,
        'order_sub' => 4,
        'parent_id' => 15,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 20,
        'title' => 'Permission',
        'url' => 'permission.index',
        'icon'=> null,
        'order_num' => null,
        'order_sub' => 5,
        'parent_id' => 15,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 21,
        'title' => 'Konfigurasi Aplikasi',
        'url' => 'configuration.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 6,
        'parent_id' => 15,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 22,
        'title' => 'E-Learning',
        'url' => '#',
        'icon' => 'icon icon-book',
        'order_num' => 14,
        'order_sub' => null,
        'parent_id' => 0,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 23,
        'title' => 'Materi',
        'url' => 'material.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 1,
        'parent_id' => 22,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 24,
        'title' => 'Belajar',
        'url' => 'learning.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 1,
        'parent_id' => 22,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 25,
        'title' => 'Computer Based Test',
        'url' => '#',
        'icon' => 'icon icon-laptop',
        'order_num' => 14,
        'order_sub' => null,
        'parent_id' => 0,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 26,
        'title' => 'Bank Soal',
        'url' => 'question.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 1,
        'parent_id' => 25,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 27,
        'title' => 'Peraturan Ujian',
        'url' => 'rules.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 2,
        'parent_id' => 25,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 28,
        'title' => 'Kelola Ujian',
        'url' => 'manage.exam.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 3,
        'parent_id' => 25,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 29,
        'title' => 'Remedial',
        'url' => 'remedial.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 4,
        'parent_id' => 25,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 30,
        'title' => 'Ujian Susulan',
        'url' => 'supplementary.exam.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 5,
        'parent_id' => 25,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 31,
        'title' => 'Akun Zoom',
        'url' => '#',
        'icon' => 'icon icon-video-camera',
        'order_num' => 14,
        'order_sub' => null,
        'parent_id' => 0,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 32,
        'title' => 'Generate User',
        'url' => 'zoom.generate.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 1,
        'parent_id' => 31,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 33,
        'title' => 'Daftar Meeting',
        'url' => 'meeting.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 2,
        'parent_id' => 31,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 34,
        'title' => 'Progress Ujian',
        'url' => 'progress.index',
        'icon' => null,
        'order_num' => null,
        'order_sub' => 6,
        'parent_id' => 25,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
    ];

    foreach ($navigations as $navigation) {
      Navigation::updateOrCreate(['id' => $navigation['id']], $navigation);
    }
  }
}
