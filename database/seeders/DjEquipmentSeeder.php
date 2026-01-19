<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DjEquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'Pioneer DJ',
            'Denon DJ',
            'Numark',
            'Rane',
            'Allen & Heath',
            'Technics',
            'Reloop',
            'Traktor',
            'Serato',
            'Virtual DJ',
            'Ableton',
            'Roland',
            'Korg',
            'Akai',
            'Mackie',
            'Behringer',
            'Yamaha',
            'JBL',
            'QSC',
            'Electro-Voice',
            'Sennheiser',
            'Shure',
            'Audio-Technica',
            'Sony',
            'V-Moda',
            'KRK',
            'Genelec',
            'Adam Audio',
            'Focal',
            'Presonus',
            'Focusrite',
            'Universal Audio',
            'Native Instruments'
        ];

        foreach ($brands as $brand) {
            \App\Models\Brand::firstOrCreate(
                ['name' => $brand],
                ['slug' => \Illuminate\Support\Str::slug($brand)]
            );
        }

        $types = [
            'Controller',
            'Mixer',
            'Multi Player (CDJ/XDJ)',
            'Turntable',
            'All-in-One System',
            'Sampler / Sequencer',
            'Effects Unit',
            'Headphones',
            'Monitor Speakers',
            'PA System',
            'Microphone',
            'Laptop / Computer',
            'Software / DVS',
            'Cables / Accessories',
            'Furniture / Booth'
        ];

        foreach ($types as $type) {
            \App\Models\EquipmentType::firstOrCreate(
                ['name' => $type],
                ['slug' => \Illuminate\Support\Str::slug($type)]
            );
        }
        // Seed Common Models
        $models = [
            'Pioneer DJ' => [
                'Multi Player (CDJ/XDJ)' => ['CDJ-3000', 'CDJ-2000NXS2', 'XDJ-1000MK2', 'XDJ-700'],
                'Mixer' => ['DJM-A9', 'DJM-V10', 'DJM-900NXS2', 'DJM-S11', 'DJM-S7'],
                'Controller' => ['DDJ-FLX10', 'DDJ-REV7', 'DDJ-1000', 'Opus-Quad'],
                'All-in-One System' => ['XDJ-XZ', 'XDJ-RX3', 'Omnis-Duo'],
            ],
            'Allen & Heath' => [
                'Mixer' => ['Xone:96', 'Xone:92', 'Xone:PX5', 'Xone:23'],
            ],
            'Technics' => [
                'Turntable' => ['SL-1200MK7', 'SL-1200GR', 'SL-1210MK2'],
            ],
            'Denon DJ' => [
                'Multi Player (CDJ/XDJ)' => ['SC6000 Prime', 'SC Live 4'],
                'Mixer' => ['X1850 Prime'],
            ]
        ];

        foreach ($models as $brandName => $types) {
            $brand = \App\Models\Brand::where('name', $brandName)->first();
            if (!$brand)
                continue;

            foreach ($types as $typeName => $modelNames) {
                $type = \App\Models\EquipmentType::where('name', $typeName)->first();
                if (!$type)
                    continue;

                foreach ($modelNames as $modelName) {
                    \App\Models\EquipmentModel::firstOrCreate(
                        [
                            'brand_id' => $brand->id,
                            'name' => $modelName
                        ],
                        [
                            'equipment_type_id' => $type->id,
                            'slug' => \Illuminate\Support\Str::slug($modelName),
                            'is_verified' => true
                        ]
                    );
                }
            }
        }
    }
}
