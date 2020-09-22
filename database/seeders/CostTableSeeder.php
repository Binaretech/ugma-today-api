<?php

namespace Database\Seeders;

use App\Models\{
    Cost,
    User
};
use Illuminate\Database\Seeder;
use Laravel\Passport\Passport;

class CostTableSeeder extends Seeder
{
    public function __construct()
    {
        $this->user = User::admin()->active()->first();
        $this->names = collect([
            'Odontología',
            'Administración',
            'Derecho',
            'Ingeniería',
            'Documentos',
        ]);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Passport::actingAs($this->user, ['admin']);
        $this->names->each(function ($name) {
            Cost::factory()->create([
                'name' => $name,
            ]);
        });
    }
}
