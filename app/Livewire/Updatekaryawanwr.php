<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Company;
use App\Models\Jabatan;
use Livewire\Component;
use App\Models\Karyawan;
use App\Models\Placement;
use App\Models\Department;
use Livewire\Attributes\On;
use App\Rules\FileSizeLimit;
use Livewire\Attributes\Url;
use App\Models\Applicantfile;
use Livewire\WithFileUploads;
use App\Livewire\Karyawanindexwr;
use App\Rules\AllowedFileExtension;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\RequiredIf;
use Google\Service\YouTube\ThirdPartyLinkStatus;
use Intervention\Image\ImageManagerStatic as Image;

class Updatekaryawanwr extends Component
{
    use WithFileUploads;

    public $id;
    public $id_karyawan, $nama, $email, $hp, $telepon, $tempat_lahir, $tanggal_lahir, $gender, $status_pernikahan, $golongan_darah, $agama, $etnis;
    public $jenis_identitas, $no_identitas, $alamat_identitas, $alamat_tinggal;
    public $status_karyawan, $tanggal_bergabung, $tanggal_resigned, $tanggal_blacklist,  $company_id, $placement_id,  $department_id, $jabatan_id, $level_jabatan, $nama_bank, $nomor_rekening;
    public $gaji_pokok, $gaji_overtime, $gaji_shift_malam_satpam, $metode_penggajian,  $bonus, $tunjangan_jabatan, $tunjangan_bahasa;
    public $tunjangan_skill, $tunjangan_lembur_sabtu, $tunjangan_lama_kerja,  $iuran_air, $iuran_locker, $denda, $gaji_bpjs, $potongan_JHT, $potongan_JP, $potongan_JKK, $potongan_JKM;
    public  $potongan_kesehatan, $update;
    public  $no_npwp, $ptkp, $status_off;
    public $kontak_darurat, $hp1, $hp2;
    public $tanggungan, $id_file_karyawan;
    public $show_arsip, $personal_files;
    public $files = [];
    public $filenames = [];
    public $is_update;
    public $pilih_jabatan;
    public $pilih_company;
    public $pilih_department;
    public $pilih_placement;
    public $delete_id;



    public function deleteFile($id)
    {
        // $id = $this->delete_id;
        // $data = Applicantfile::where('filename', $filename)->first();
        $data = Applicantfile::find($id);
        if ($data != null) {
            try {
                // $result = Storage::disk('google')->delete($data->filename);
                $result = Storage::disk('public')->delete($data->filename);
                if ($result) {
                    // File was deleted successfully
                    $data->delete();
                    // $this->dispatch('success', message: 'File telah di delete');
                    $this->dispatch(
                        'message',
                        type: 'success',
                        title: 'File telah di delete',
                        position: 'center'
                    );

                    return 'File deleted successfully.';
                } else {
                    // File could not be deleted
                    // return 'Failed to delete file.';


                    // $this->dispatch('error', message: 'File GAGAL di delete');
                    $this->dispatch(
                        'message',
                        type: 'error',
                        title: 'File GAGAL di delete',
                        position: 'center'
                    );
                }
            } catch (\Exception $e) {
                // An error occurred while deleting the file
                return 'An error occurred: ' . $e->getMessage();
            }
        } else {
            // $this->dispatch('error', message: 'File tidak ketemu');
            $this->dispatch(
                'message',
                type: 'error',
                title: 'File tidak ketemu',
                position: 'center'
            );
        }
    }



    public function updatedFiles()
    {
        $this->validate(
            [
                // 'files.*' => ['nullable', 'mimes:png,jpg,jpeg', new AllowedFileExtension],
                'files.*' => ['nullable', new AllowedFileExtension],
            ],
            // [
            //     'files.*.mimes' => ['Hanya menerima file png dan jpg'],
            // ]
        );
    }





    public function arsip()
    {
        $this->show_arsip = true;
    }

    public function tutup_arsip()
    {
        $this->show_arsip = false;
    }

    public function mount($id)
    {
        $this->pilih_jabatan = Jabatan::orderBy('nama_jabatan', 'asc')->get();
        $this->pilih_company = Company::orderBy('company_name', 'asc')->get();
        $this->pilih_department = Department::orderBy('nama_department', 'asc')->get();
        $this->pilih_placement = Placement::orderBy('placement_name', 'asc')->get();



        $this->is_update = true;
        $this->show_arsip = false;
        $this->status_off = false;
        $this->update = true;
        $this->id = $id;
        $data = Karyawan::find($id);
        $this->id_karyawan = $data->id_karyawan;
        $this->nama = $data->nama;
        $this->email = trim($data->email);
        $this->hp = $data->hp;
        $this->telepon = $data->telepon;
        $this->tempat_lahir = $data->tempat_lahir;
        //  $this->tanggal_lahir = $data->tanggal_lahir;
        $this->tanggal_lahir =  date('d M Y', strtotime($data->tanggal_lahir));

        $this->gender = $data->gender;
        $this->status_pernikahan = trim($data->status_pernikahan);
        $this->golongan_darah = trim($data->golongan_darah);
        $this->agama = trim($data->agama);
        $this->etnis = trim($data->etnis);
        $this->kontak_darurat = trim($data->kontak_darurat);
        $this->hp1 = trim($data->hp1);
        $this->hp2 = trim($data->hp2);


        // Identitas
        $this->jenis_identitas = trim($data->jenis_identitas);
        $this->no_identitas = $data->no_identitas;
        $this->alamat_identitas = $data->alamat_identitas;
        $this->alamat_tinggal = $data->alamat_tinggal;

        //Data Kepegawaian
        $this->status_karyawan = trim($data->status_karyawan);
        $this->tanggal_bergabung =  date('d M Y', strtotime($data->tanggal_bergabung));
        $this->tanggal_resigned = $data->tanggal_resigned;
        $this->tanggal_blacklist = $data->tanggal_blacklist;

        $this->company_id = $data->company_id;
        $this->placement_id = $data->placement_id;
        $this->department_id = $data->department_id;
        $this->jabatan_id = $data->jabatan_id;

        if ($this->jabatan_id == 100) {
            $this->jabatan_id = '';
        }
        if ($this->company_id == 100) {
            $this->company_id = '';
        }
        if ($this->department_id == 100) {
            $this->department_id = '';
        }
        if ($this->placement_id == 100) {
            $this->placement_id = '';
        }
        $this->level_jabatan = trim($data->level_jabatan);
        $this->nama_bank = trim($data->nama_bank);
        $this->nomor_rekening = $data->nomor_rekening;

        //Payroll
        $this->metode_penggajian = trim($data->metode_penggajian);
        //  $this->gaji_pokok = $data->gaji_pokok;
        $this->gaji_pokok = $data->gaji_pokok;
        $this->gaji_overtime = $data->gaji_overtime;
        $this->gaji_shift_malam_satpam = $data->gaji_shift_malam_satpam;
        $this->bonus = $data->bonus;
        $this->tunjangan_jabatan = $data->tunjangan_jabatan;
        $this->tunjangan_bahasa = $data->tunjangan_bahasa;
        $this->tunjangan_skill = $data->tunjangan_skill;
        $this->tunjangan_lembur_sabtu = $data->tunjangan_lembur_sabtu;
        $this->tunjangan_lama_kerja = $data->tunjangan_lama_kerja;
        $this->iuran_air = $data->iuran_air;
        $this->denda = $data->denda;
        $this->iuran_locker = $data->iuran_locker;
        $this->gaji_bpjs = $data->gaji_bpjs;
        $this->potongan_JHT = $data->potongan_JHT;
        $this->potongan_JP = $data->potongan_JP;
        $this->potongan_JKK = $data->potongan_JKK;
        $this->potongan_JKM = $data->potongan_JKM;
        $this->potongan_kesehatan = $data->potongan_kesehatan;
        $this->tanggungan = $data->tanggungan;
        $this->no_npwp = $data->no_npwp;
        $this->ptkp = $data->ptkp;
        $this->id_file_karyawan = $data->id_file_karyawan;

        // data Applicant files
        $this->personal_files = Applicantfile::where('id_karyawan', $this->id_file_karyawan)->get();
    }

    // Cara benerin email unique agar bisa di update

    public function rules()
    {
        return [
            'files.*' =>  ['nullable', 'mimes:png,jpg,jpeg', new AllowedFileExtension],
            // 'files.*' =>  ['nullable', new AllowedFileExtension],
            'id_karyawan' => 'required',
            'nama' => 'required',
            'email' => 'email|nullable|unique:karyawans,email,' . $this->id,
            'tanggal_lahir' => 'date|before:today|required',
            // PRIBADI
            'hp' => 'nullable',
            'telepon' => 'nullable',
            'tempat_lahir' => 'required',
            'gender' => 'required',
            'status_pernikahan' => 'nullable',
            'golongan_darah' => 'nullable',
            'agama' => 'nullable',
            'etnis' => 'required',
            'kontak_darurat' => 'nullable',
            'hp1' => 'nullable',
            'hp2' => 'nullable',

            // IDENTITAS
            'jenis_identitas' => 'required',
            'no_identitas' => 'required',
            'alamat_identitas' => 'required',
            'alamat_tinggal' => 'required',
            // KEPEGAWAIAN
            'status_karyawan' => 'required',
            'tanggal_resigned' => new RequiredIf($this->status_karyawan == 'Resigned'),
            'tanggal_blacklist' => new RequiredIf($this->status_karyawan == 'Blacklist'),
            'tanggal_bergabung' => 'date|required',
            'company_id' => 'required',
            'placement_id' => 'required',
            'department_id' => 'required',
            'jabatan_id' => 'required',
            'level_jabatan' => 'nullable',
            'nama_bank' => 'nullable',
            'nomor_rekening' => 'nullable',
            // PAYROLL
            'metode_penggajian' => 'required',
            'gaji_pokok' => 'numeric|required',
            'gaji_overtime' => 'numeric|required',
            'gaji_shift_malam_satpam' => 'numeric',
            'bonus' => 'numeric|nullable',
            'tunjangan_jabatan' => 'numeric|nullable',
            'tunjangan_bahasa' => 'numeric|nullable',
            'tunjangan_skill' => 'numeric|nullable',
            'tunjangan_lembur_sabtu' => 'numeric|nullable',
            'tunjangan_lama_kerja' => 'numeric|nullable',
            'iuran_air' => 'numeric|required',
            'denda' => 'numeric|nullable',
            'iuran_locker' => 'numeric|nullable',
            'gaji_bpjs' => 'nullable',
            'potongan_JHT' => 'nullable',
            'potongan_JP' => 'nullable',
            'potongan_JKK' => 'nullable',
            'potongan_JKM' => 'nullable',
            'potongan_kesehatan' => 'nullable',
            'tanggungan' => 'nullable',
            'no_npwp' => 'nullable',
            'ptkp' => 'nullable',
        ];
    }

    public function uploadfile()
    {
        dd('sini');
        $this->validate([
            'files.*' =>  ['nullable', 'mimes:png,jpg,jpeg', new AllowedFileExtension]
        ], [
            'files.*.mimes' => ['Hanya menerima file png dan jpg'],
        ]);
        if ($this->files) {
            if (!$this->id_file_karyawan) {
                // convertTgl adalah fungsi untuk merubah format tanggal menjadi format sesuai system
                $this->id_file_karyawan = makeApplicationId($this->nama, convertTgl($this->tanggal_lahir));
                $data = Karyawan::find($this->id);
                $data->id_file_karyawan = $this->id_file_karyawan;
                $data->save();
            }

            foreach ($this->files as $file) {
                $folder = 'Applicants/' . $this->id_file_karyawan;
                $fileExension = $file->getClientOriginalExtension();

                if ($fileExension != 'pdf') {
                    $folder = 'Applicants/' . $this->id_file_karyawan . '/' . random_int(1000, 9000) . '.' . $fileExension;

                    $manager = ImageManager::gd();

                    // resize gif image
                    $image = $manager
                        ->read($file)
                        ->scale(width: 800);
                    // $imagedata = (string) $image->toJpeg();
                    $imagedata = (string) $image->toWebp(60);

                    // Storage::disk('google')->put($folder, $imagedata);
                    Storage::disk('public')->put($folder, $imagedata);
                    $this->path = $folder;
                } else {
                    // $this->path = Storage::disk('google')->put($folder, $file);
                    $this->path = Storage::disk('public')->put($folder, $file);
                }

                $this->originalFilename = $file->getClientOriginalName();
                Applicantfile::create([
                    'id_karyawan' => $this->id_file_karyawan,
                    'originalName' => clear_dot($this->originalFilename, $fileExension),
                    'filename' => $this->path,
                ]);
            }
            $this->files = [];
            // $this->dispatch('success', message: 'file berhasil di upload');
            $this->dispatch(
                'message',
                type: 'success',
                title: 'file berhasil di upload',
                position: 'center'
            );
        }
    }

    public function update1()
    {
        $this->gaji_pokok = convert_numeric($this->gaji_pokok);
        $this->gaji_overtime = convert_numeric($this->gaji_overtime);
        $this->gaji_shift_malam_satpam = convert_numeric($this->gaji_shift_malam_satpam);
        $this->bonus = convert_numeric($this->bonus);
        $this->tunjangan_jabatan = convert_numeric($this->tunjangan_jabatan);
        $this->tunjangan_bahasa = convert_numeric($this->tunjangan_bahasa);
        $this->tunjangan_skill = convert_numeric($this->tunjangan_skill);
        $this->tunjangan_lembur_sabtu = convert_numeric($this->tunjangan_lembur_sabtu);
        $this->tunjangan_lama_kerja = convert_numeric($this->tunjangan_lama_kerja);
        $this->iuran_air = convert_numeric($this->iuran_air);
        $this->iuran_locker = convert_numeric($this->iuran_locker);
        $this->gaji_bpjs = convert_numeric($this->gaji_bpjs);
        $this->denda = convert_numeric($this->denda);
        $this->validate();
        $this->tanggal_lahir = date('Y-m-d', strtotime($this->tanggal_lahir));
        $this->tanggal_bergabung = date('Y-m-d', strtotime($this->tanggal_bergabung));
        $data = Karyawan::find($this->id);

        // Ini untuk merubah nama folder menyesuaikan dengan nama dan tanggal lahir yang dirubah
        // if ((strtolower($data->nama) != strtolower($this->nama)) || ($data->tanggal_lahir != $this->tanggal_lahir)) {
        //     // rubah folder storage
        //     $old_folder = 'Applicants/' . $data->id_file_karyawan;
        //     $new_applicant_id = makeApplicationId($this->nama, $this->tanggal_lahir);
        //     $new_folder = 'Applicants/' . $new_applicant_id;
        //     Storage::disk('public')->move($old_folder, $new_folder);
        //     // Storage::disk('google')->move($old_folder, $new_folder);
        //     $this->id_file_karyawan = $new_applicant_id;
        //     // rubah id karyawan di appplicantfile

        //     $data_files = Applicantfile::where('id_karyawan', $data->id_file_karyawan)->get();
        //     if ($data_files != null) {
        //         foreach ($data_files as $df) {
        //             $df->id_karyawan = $new_applicant_id;
        //             $df->filename = $new_folder . '/' . getFilename($df->filename);
        //             $df->save();
        //         }
        //     } else {
        //         dd('not found', $data->id_file_karyawan);
        //     }
        // }
        $data->id_karyawan = $this->id_karyawan;
        $data->nama = titleCase($this->nama);
        $data->email = trim($this->email, ' ');
        $data->hp = $this->hp;
        $data->telepon = $this->telepon;
        $data->tempat_lahir = titleCase($this->tempat_lahir);
        $data->tanggal_lahir = $this->tanggal_lahir;
        $data->gender = $this->gender;
        $data->status_pernikahan = $this->status_pernikahan;
        $data->golongan_darah = $this->golongan_darah;
        $data->agama = $this->agama;
        $data->etnis = $this->etnis;
        $data->kontak_darurat = $this->kontak_darurat;
        $data->hp1 = $this->hp1;
        $data->hp2 = $this->hp2;

        // Identitas
        $data->jenis_identitas = $this->jenis_identitas;
        $data->no_identitas = $this->no_identitas;
        $data->alamat_identitas = titleCase($this->alamat_identitas);
        $data->alamat_tinggal = titleCase($this->alamat_tinggal);
        // Data Kepegawaian
        $data->status_karyawan = $this->status_karyawan;
        $data->tanggal_bergabung = $this->tanggal_bergabung;
        if ($this->status_karyawan == 'Resigned') {
            $data->tanggal_blacklist = null;
            $data->tanggal_resigned = $this->tanggal_resigned;
            $data->email = 'resigned_' . trim($this->email);
        } elseif ($this->status_karyawan == 'Blacklist') {

            $data->tanggal_resigned = null;
            $data->tanggal_blacklist = $this->tanggal_blacklist;
            $data->email = 'blacklist_' . trim($this->email);
        } else {
            $data->tanggal_blacklist = null;
            $data->tanggal_resigned = null;
        }

        $data->company_id = $this->company_id;
        $data->placement_id = $this->placement_id;
        $data->department_id = $this->department_id;
        $data->jabatan_id = $this->jabatan_id;
        $data->level_jabatan = $this->level_jabatan;
        $data->nama_bank = $this->nama_bank;
        $data->nomor_rekening = $this->nomor_rekening;

        // Payroll
        $data->gaji_pokok = $this->gaji_pokok;
        $data->gaji_overtime = $this->gaji_overtime;
        $data->gaji_shift_malam_satpam = $this->gaji_shift_malam_satpam;
        $data->metode_penggajian = $this->metode_penggajian;
        $data->bonus = $this->bonus;
        $data->tunjangan_jabatan = $this->tunjangan_jabatan;
        $data->tunjangan_bahasa = $this->tunjangan_bahasa;
        $data->tunjangan_skill = $this->tunjangan_skill;
        $data->tunjangan_lembur_sabtu = $this->tunjangan_lembur_sabtu;
        $data->tunjangan_lama_kerja = $this->tunjangan_lama_kerja;
        $data->iuran_air = $this->iuran_air;
        $data->iuran_locker = $this->iuran_locker;
        $data->gaji_bpjs = $this->gaji_bpjs;
        $data->potongan_JHT = $this->potongan_JHT;
        $data->potongan_JP = $this->potongan_JP;
        $data->potongan_JKK = $this->potongan_JKK;
        $data->potongan_JKM = $this->potongan_JKM;
        $data->potongan_kesehatan = $this->potongan_kesehatan;
        $data->tanggungan = $this->tanggungan;
        $data->no_npwp = $this->no_npwp;
        $data->ptkp = $this->ptkp;
        $data->denda = $this->denda;
        $data->id_file_karyawan = $this->id_file_karyawan;

        $data->save();


        $dataUser = User::where('username', $data->id_karyawan)->first();
        // if ( $dataUser->id != null ) {
        if ($dataUser->id) {
            $user = User::find($dataUser->id);
            $user->name = titleCase($this->nama);
            $user->email = trim($this->email, ' ');
            $user->save();
            $this->tanggal_lahir = date('d M Y', strtotime($this->tanggal_lahir));
            $this->tanggal_bergabung = date('d M Y', strtotime($this->tanggal_bergabung));
            // $this->dispatch('success', message: 'Data Karyawan Sudah di Update');
            $this->dispatch(
                'message',
                type: 'success',
                title: 'Data Karyawan Sudah di Update',
                position: 'center'
            );
        } else {
            $this->tanggal_lahir = date('d M Y', strtotime($this->tanggal_lahir));
            $this->tanggal_bergabung = date('d M Y', strtotime($this->tanggal_bergabung));
            // $this->dispatch('info', message: 'Data Karyawan Sudah di Update, User tidak terupdate');
            $this->dispatch(
                'message',
                type: 'success',
                title: 'Data Karyawan Sudah di Update, User tidak terupdate',
                position: 'center'
            );
        }

        if ($this->files) {
            if (!$this->id_file_karyawan) {
                // convertTgl adalah fungsi untuk merubah format tanggal menjadi format sesuai system
                $this->id_file_karyawan = makeApplicationId($this->nama, convertTgl($this->tanggal_lahir));
                $data = Karyawan::find($this->id);
                $data->id_file_karyawan = $this->id_file_karyawan;
                $data->save();
            }

            foreach ($this->files as $file) {
                $folder = 'Applicants/' . $this->id_file_karyawan;
                $fileExension = $file->getClientOriginalExtension();

                if ($fileExension != 'pdf') {
                    $folder = 'Applicants/' . $this->id_file_karyawan . '/' . random_int(1000, 9000) . '.' . $fileExension;

                    $manager = ImageManager::gd();

                    // resize gif image
                    $image = $manager
                        ->read($file)
                        ->scale(width: 800);
                    // $imagedata = (string) $image->toJpeg();
                    $imagedata = (string) $image->toWebp(60);

                    // Storage::disk('google')->put($folder, $imagedata);
                    Storage::disk('public')->put($folder, $imagedata);
                    $this->path = $folder;
                } else {
                    // $this->path = Storage::disk('google')->put($folder, $file);
                    $this->path = Storage::disk('public')->put($folder, $file);
                }

                $this->originalFilename = $file->getClientOriginalName();
                Applicantfile::create([
                    'id_karyawan' => $this->id_file_karyawan,
                    'originalName' => clear_dot($this->originalFilename, $fileExension),

                    'filename' => $this->path,
                ]);
            }
            $this->files = [];
            // $this->dispatch('success', message: 'Data berhasil di update');
        }
        // get_data_karyawan();
    }


    public function exit()
    {
        // $this->reset();
        return redirect()->to('/karyawanindex');
    }

    public function render()
    {
        $this->personal_files = Applicantfile::where('id_karyawan', $this->id_file_karyawan)->get();

        return view('livewire.updatekaryawanwr')
            ->layout('layouts.appeloe');
    }
}
