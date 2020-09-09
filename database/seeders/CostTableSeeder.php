<?php

use App\Cost;
use App\User;
use Illuminate\Database\Seeder;
use Laravel\Passport\Passport;

class CostTableSeeder extends Seeder
{
    public function __construct()
    {
        $this->users = User::admin()->active()->get();
        $this->names = collect([
            'Odontología',
            'Administracion',
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
        $this->names->each(function ($name) {
            Passport::actingAs($this->users->random());
            factory(Cost::class)->create([
                'name' => $name,
            ]);
        });
    }
}
