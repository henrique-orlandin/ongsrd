<?php

namespace App\Database\Seeds;

use App\Models\AboutModel;
use App\Models\ContactMessageModel;
use App\Models\HappyEndingModel;
use App\Models\HomePageSettingModel;
use App\Models\HowToHelpModel;
use App\Models\PartnerModel;
use App\Models\PetModel;
use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;

class CmsSeeder extends Seeder
{
    public function run()
    {
        $this->seedUsers();

        (new AboutModel())->insert([
            'title' => 'Who We Are',
            'description' => 'We rescue vulnerable pets and help them find loving families.',
        ]);

        (new HowToHelpModel())->insert([
            'title' => 'How You Can Help',
            'description' => 'You can help by fostering pets, donating supplies, and sharing adoption campaigns.',
        ]);

        (new HomePageSettingModel())->insert([
            'description' => 'A safe place for rescued pets to recover and find new homes.',
        ]);

        (new HappyEndingModel())->insert([
            'title' => 'Luna Found a Family',
            'description' => 'After 6 months in foster care, Luna was adopted by a loving family.',
        ]);

        (new PetModel())->insert([
            'name' => 'Milo',
            'type' => 'Dog',
            'age' => 2,
            'size' => 'M',
            'gender' => 'Male',
            'description' => 'Friendly and playful dog looking for a forever home.',
        ]);

        (new PartnerModel())->insert([
            'name' => 'Local Vet Clinic',
            'image' => '',
        ]);

        (new ContactMessageModel())->insert([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+1 555-1234',
            'message' => 'I would like to volunteer during weekends.',
            'is_new' => 1,
        ]);
    }

    private function seedUsers(): void
    {
        $users = model(setting('Auth.userProvider'));

        if (! $users instanceof UserModel) {
            return;
        }

        $defaults = [
            [
                'username' => 'superadmin',
                'email' => 'superadmin@ongsrd.local',
                'password' => 'SuperAdmin123!',
                'group' => 'super_admin',
            ],
            [
                'username' => 'admin',
                'email' => 'admin@ongsrd.local',
                'password' => 'Admin12345!',
                'group' => 'admin',
            ],
        ];

        foreach ($defaults as $data) {
            $exists = $users->findByCredentials(['email' => $data['email']]);
            if ($exists !== null) {
                continue;
            }

            $user = $users->createNewUser([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $users->save($user);

            $created = $users->findById($users->getInsertID());
            if ($created !== null) {
                $created->activate();
                $created->addGroup($data['group']);
            }
        }
    }
}
