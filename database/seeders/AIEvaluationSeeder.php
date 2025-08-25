<?php

namespace Database\Seeders;

use App\Models\AIEvaluation;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class AIEvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing companies and users
        $companies = Company::all();
        $users = User::all();

        if ($companies->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No companies or users found. Skipping AI evaluation seeding.');
            return;
        }

        $this->command->info('Membuat data sampel evaluasi AI...');

        // Create sample evaluations for each company
        foreach ($companies as $company) {
            $companyUsers = $users->where('id', $company->owner)->first();
            if (!$companyUsers) continue;

            // Sales Order Evaluation
            AIEvaluation::create([
                'company_id' => $company->id,
                'category' => AIEvaluation::CATEGORY_SALES_ORDER,
                'title' => 'Analisis Kinerja Penjualan Q4 2024',
                'content' => "Berdasarkan analisis data penjualan dari Q4 2024, perusahaan telah menunjukkan kinerja yang kuat di beberapa area kunci. Total pendapatan telah meningkat 15% dibandingkan Q3, dengan tingkat akuisisi pelanggan yang meningkat secara signifikan. Produk-produk berkinerja tinggi terus mendorong mayoritas penjualan, sementara lini produk baru menunjukkan potensi pertumbuhan yang menjanjikan.\n\nObservasi kunci meliputi peningkatan tingkat retensi pelanggan dan produktivitas tim penjualan yang ditingkatkan. Data menunjukkan bahwa kampanye pemasaran baru-baru ini telah efektif dalam mendorong keterlibatan pelanggan dan konversi penjualan.",
                'data_summary' => [
                    'summary' => [
                        'total_orders' => 1250,
                        'total_revenue' => 875000,
                        'completed_orders' => 1180,
                        'pending_orders' => 45,
                        'overdue_orders' => 25,
                        'completion_rate' => 94.4,
                    ],
                    'monthly_trends' => [
                        ['month' => 'Oct 2024', 'total_orders' => 380, 'total_revenue' => 265000],
                        ['month' => 'Nov 2024', 'total_orders' => 420, 'total_revenue' => 295000],
                        ['month' => 'Dec 2024', 'total_orders' => 450, 'total_revenue' => 315000],
                    ],
                ],
                'insights' => [
                    'Kinerja penjualan menunjukkan pertumbuhan yang konsisten selama periode yang dianalisis',
                    'Tingkat retensi pelanggan di atas rata-rata industri sebesar 94.4%',
                    'Periode penjualan puncak selaras dengan pola bisnis musiman',
                    'Lini produk baru mendapatkan daya tarik pasar',
                ],
                'recommendations' => [
                    'Fokus pada perluasan basis pelanggan selama periode puncak',
                    'Pertimbangkan implementasi program loyalitas pelanggan',
                    'Optimalkan manajemen inventaris untuk permintaan musiman',
                    'Investasikan pelatihan untuk tim penjualan untuk meningkatkan tingkat konversi',
                ],
                'evaluation_date' => now(),
                'period_start' => '2024-10-01',
                'period_end' => '2024-12-31',
                'status' => AIEvaluation::STATUS_COMPLETED,
                'generated_by' => $companyUsers->id,
            ]);

            // Purchase Order Evaluation
            AIEvaluation::create([
                'company_id' => $company->id,
                'category' => AIEvaluation::CATEGORY_PURCHASE_ORDER,
                'title' => 'Tinjauan Efisiensi Pengadaan Q4 2024',
                'content' => "Analisis pengadaan untuk Q4 2024 mengungkapkan beberapa area peningkatan dalam manajemen pemasok dan optimasi biaya. Meskipun biaya pengadaan secara keseluruhan tetap stabil, ada peluang untuk meningkatkan hubungan dengan pemasok dan merampingkan proses pembelian.\n\nData menunjukkan bahwa beberapa pemasok secara konsisten mengirim tepat waktu dan mempertahankan standar kualitas, sementara yang lain mungkin memerlukan perhatian tambahan. Analisis juga menunjukkan bahwa strategi pembelian dalam jumlah besar telah efektif dalam mengurangi biaya per unit untuk item volume tinggi.",
                'data_summary' => [
                    'summary' => [
                        'total_orders' => 890,
                        'total_amount' => 650000,
                        'completed_orders' => 845,
                        'pending_orders' => 35,
                        'overdue_orders' => 10,
                        'completion_rate' => 94.9,
                    ],
                    'monthly_trends' => [
                        ['month' => 'Oct 2024', 'total_orders' => 280, 'total_amount' => 205000],
                        ['month' => 'Nov 2024', 'total_orders' => 300, 'total_amount' => 220000],
                        ['month' => 'Dec 2024', 'total_orders' => 310, 'total_amount' => 225000],
                    ],
                ],
                'insights' => [
                    'Efisiensi pengadaan telah meningkat dengan tingkat penyelesaian 94.9%',
                    'Kinerja pemasok bervariasi secara signifikan di berbagai kategori',
                    'Strategi pembelian dalam jumlah besar efektif untuk pengurangan biaya',
                    'Waktu tunggu telah membaik untuk sebagian besar kategori produk',
                ],
                'recommendations' => [
                    'Kembangkan metrik kinerja untuk evaluasi pemasok',
                    'Implementasikan sourcing strategis untuk item bernilai tinggi',
                    'Pertimbangkan kontrak jangka panjang dengan pemasok berkinerja tinggi',
                    'Buat pertemuan tinjauan pemasok secara rutin',
                ],
                'evaluation_date' => now(),
                'period_start' => '2024-10-01',
                'period_end' => '2024-12-31',
                'status' => AIEvaluation::STATUS_COMPLETED,
                'generated_by' => $companyUsers->id,
            ]);

            // Financial Position Evaluation
            AIEvaluation::create([
                'company_id' => $company->id,
                'category' => AIEvaluation::CATEGORY_FINANCIAL_POSITION,
                'title' => 'Penilaian Kesehatan Keuangan Q4 2024',
                'content' => "Analisis posisi keuangan untuk Q4 2024 menunjukkan kondisi keuangan yang sehat dan stabil. Perusahaan telah mempertahankan manajemen arus kas yang kuat dan berhasil mengendalikan biaya operasional sambil meningkatkan pendapatan.\n\nMetrik keuangan kunci menunjukkan tren positif dalam profitabilitas dan pemanfaatan aset. Neraca mencerminkan posisi likuiditas yang kuat dengan modal kerja yang memadai untuk mendukung operasi yang sedang berlangsung dan inisiatif pertumbuhan masa depan.",
                'data_summary' => [
                    'summary' => [
                        'total_transactions' => 2150,
                        'total_debits' => 1200000,
                        'total_credits' => 1180000,
                        'net_position' => 20000,
                    ],
                    'monthly_trends' => [
                        ['month' => 'Oct 2024', 'total_transactions' => 680, 'total_debits' => 380000, 'total_credits' => 375000],
                        ['month' => 'Nov 2024', 'total_transactions' => 720, 'total_debits' => 400000, 'total_credits' => 395000],
                        ['month' => 'Dec 2024', 'total_transactions' => 750, 'total_debits' => 420000, 'total_credits' => 410000],
                    ],
                ],
                'insights' => [
                    'Posisi keuangan tetap kuat dengan posisi bersih yang positif',
                    'Volume transaksi telah meningkat secara konsisten bulan demi bulan',
                    'Manajemen arus kas efektif dan berkelanjutan',
                    'Biaya operasional dikendalikan dengan baik relatif terhadap pendapatan',
                ],
                'recommendations' => [
                    'Lanjutkan pemantauan pola arus kas untuk variasi musiman',
                    'Pertimbangkan peluang investasi untuk cadangan kas berlebih',
                    'Pertahankan langkah-langkah pengendalian biaya saat ini',
                    'Kembangkan rencana kontingensi untuk ketidakpastian ekonomi',
                ],
                'evaluation_date' => now(),
                'period_start' => '2024-10-01',
                'period_end' => '2024-12-31',
                'status' => AIEvaluation::STATUS_COMPLETED,
                'generated_by' => $companyUsers->id,
            ]);

            // Employee Attendance Evaluation
            AIEvaluation::create([
                'company_id' => $company->id,
                'category' => AIEvaluation::CATEGORY_EMPLOYEE_ATTENDANCE,
                'title' => 'Analisis Produktivitas Tenaga Kerja Q4 2024',
                'content' => "Analisis kehadiran dan produktivitas karyawan untuk Q4 2024 menunjukkan tren positif dalam keterlibatan tenaga kerja dan pola kehadiran. Tingkat kehadiran secara keseluruhan telah membaik, dan metrik produktivitas karyawan menunjukkan penggunaan jam kerja yang efisien.\n\nData mengungkapkan bahwa sebagian besar departemen mempertahankan standar kehadiran yang tinggi, dengan minimal insiden keterlambatan atau ketidakhadiran. Analisis juga menunjukkan bahwa pengaturan kerja yang fleksibel telah berkontribusi pada peningkatan kepuasan karyawan dan produktivitas.",
                'data_summary' => [
                    'summary' => [
                        'total_records' => 5280,
                        'approved_records' => 5120,
                        'pending_records' => 120,
                        'rejected_records' => 40,
                        'approval_rate' => 97.0,
                    ],
                    'monthly_trends' => [
                        ['month' => 'Oct 2024', 'total_records' => 1760, 'approved_records' => 1705, 'pending_records' => 40],
                        ['month' => 'Nov 2024', 'total_records' => 1760, 'approved_records' => 1710, 'pending_records' => 40],
                        ['month' => 'Dec 2024', 'total_records' => 1760, 'approved_records' => 1705, 'pending_records' => 40],
                    ],
                ],
                'insights' => [
                    'Tingkat persetujuan kehadiran sangat baik sebesar 97.0%',
                    'Produktivitas karyawan menunjukkan peningkatan yang konsisten',
                    'Pengaturan kerja yang fleksibel diterima dengan baik',
                    'Kinerja departemen bervariasi sedikit tetapi tetap kuat',
                ],
                'recommendations' => [
                    'Lanjutkan pemantauan pola kehadiran untuk tanda-tanda peringatan dini',
                    'Pertimbangkan perluasan pengaturan kerja yang fleksibel',
                    'Implementasikan program pengakuan untuk karyawan berkinerja tinggi',
                    'Kembangkan program pelatihan untuk mengatasi kesenjangan produktivitas',
                ],
                'evaluation_date' => now(),
                'period_start' => '2024-10-01',
                'period_end' => '2024-12-31',
                'status' => AIEvaluation::STATUS_COMPLETED,
                'generated_by' => $companyUsers->id,
            ]);
        }

        $this->command->info('Data sampel evaluasi AI berhasil dibuat!');
    }
}
