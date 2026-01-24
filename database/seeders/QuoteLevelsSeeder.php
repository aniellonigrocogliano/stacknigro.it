<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuoteLevelsSeeder extends Seeder
{
  public function run(): void
  {
    $now = now();

    $rows = [];
    for ($i = 1; $i <= 10; $i++) {
      $rows[] = [
        'level' => $i,
        'name' => "Livello {$i}",
        'sort_order' => $i,
        'selection_type' => 'single',
        'min_select' => 0,
        'max_select' => null,
        'is_active' => true,
        'created_at' => $now,
        'updated_at' => $now,
      ];
    }

    // idempotente: se rerunni, aggiorna i 10 livelli senza duplicare
    foreach ($rows as $row) {
      DB::table('quote_levels')->updateOrInsert(
        ['level' => $row['level']],
        $row
      );
    }
  }
}
