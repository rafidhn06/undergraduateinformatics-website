<?php

namespace Tests\Feature;

use App\Models\DashboardDataset;
use App\Models\DatasetImport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class DatasetImportFlowTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(): User
    {
        return User::create([
            'email' => fake()->unique()->safeEmail(),
            'password_recovery_id' => 1,
            'password' => bcrypt('password'),
        ]);
    }

    private function buildExcelUpload(): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mahasiswa');
        $sheet->fromArray([['Tahun', 'Jumlah'], ['2023', 100], ['2024', 120]]);
        $path = tempnam(sys_get_temp_dir(), 'dataset-import') . '.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        return new UploadedFile($path, 'data.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    public function test_dataset_import_stages_and_confirms(): void
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $create = $this->get('/admin/dataset-imports/create');
        $create->assertOk();

        $store = $this->post('/admin/dataset-imports', ['excel_file' => $this->buildExcelUpload()]);
        $store->assertRedirect();
        $import = DatasetImport::query()->latest()->first();
        $this->assertNotNull($import);

        $show = $this->get("/admin/dataset-imports/{$import->token}");
        $show->assertOk();

        $confirm = $this->put("/admin/dataset-imports/{$import->token}", ['datasets' => []]);
        $confirm->assertRedirect('/admin/datasets');
    }

    public function test_dataset_import_confirm_persists_staged_payload(): void
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $this->post('/admin/dataset-imports', ['excel_file' => $this->buildExcelUpload()])->assertRedirect();
        $import = DatasetImport::query()->latest()->first();
        $this->assertNotNull($import);
        $this->assertNotEmpty($import->payload);

        $this->put("/admin/dataset-imports/{$import->token}", ['datasets' => $import->payload])
            ->assertRedirect(route('admin.datasets.index'));

        $this->assertSame(0, DatasetImport::count());
        $this->assertSame(1, DashboardDataset::count());

        $saved = DashboardDataset::with('items')->first();
        $this->assertSame('Mahasiswa', $saved->title);
        $this->assertSame(['2023', '2024'], $saved->items->pluck('label')->values()->all());
    }

    public function test_dataset_import_rejects_unreadable_file(): void
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $file = UploadedFile::fake()->create('data.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->post('/admin/dataset-imports', ['excel_file' => $file])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(0, DatasetImport::count());
    }

    public function test_dataset_import_show_expired_returns_gone(): void
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $import = DatasetImport::query()->create([
            'user_id' => $admin->id,
            'token' => 'expired-token',
            'status' => 'staged',
            'payload' => [],
            'expires_at' => now()->subHour(),
        ]);

        $this->get("/admin/dataset-imports/{$import->token}")->assertStatus(410);
        $this->put("/admin/dataset-imports/{$import->token}", ['datasets' => []])->assertStatus(410);
    }

    public function test_dataset_import_destroy_cancels_staged_import(): void
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $import = DatasetImport::query()->create([
            'user_id' => $admin->id,
            'token' => 'cancel-token',
            'status' => 'staged',
            'payload' => [],
            'expires_at' => now()->addHour(),
        ]);

        $this->delete("/admin/dataset-imports/{$import->token}")
            ->assertRedirect(route('admin.datasets.index'));

        $this->assertSame(0, DatasetImport::count());
    }

    public function test_dataset_import_requires_authentication(): void
    {
        $this->get('/admin/dataset-imports/create')->assertRedirect(route('admin.login'));
    }
}
