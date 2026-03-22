<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Aseo Hogar' => [
                'Accesorios Aseo' => [
                    'Esponjas y Paños Absorbente',
                    'Utensilios Aseo Hogar',
                    'Bolsas Basura',
                ],
                'Cuidado De La Ropa' => [
                    'Aditivos',
                    'Detergentes Líquidos',
                    'Detergentes En Polvo',
                    'Jabones En Barra',
                    'Pre-Planchado-Lavado',
                    'Suavizantes',
                ],
                'Cuidado Hogar' => [
                    'Cuidado Hogar Otros',
                    'Ceras',
                    'Ambientadores',
                    'Insecticidas',
                    'Lustradores y Limpia Muebles',
                ],
                'Limpieza Hogar' => [
                    'Acido y Varsol',
                    'Lava lozas',
                    'Limpiadores',
                    'Superficies',
                ],
                'Productos De Calzado' => [
                    'Betunes',
                ],
            ],
            'Bebidas, Pasabocas y Dulces' => [
                'Bebidas Liquidas' => [
                    'Aguas, Gaseosas y Otros',
                ],
                'Dulceria' => [
                    'Confiteria',
                    'Dulceria Tipica',
                    'Chocolates',
                ],
                'Pasabocas' => [
                    'Snacks',
                    'Pasabocas Surtidos',
                ],
            ],
            'Despensa' => [
                'Granos Empacados' => [
                    'Granos Empacados',
                    'Sal',
                ],
                'Aceites' => [
                    'Aceites Cremosos Solidos',
                    'Aceites Liquidos',
                ],
                'Alimentos Saludables' => [
                    'Alimentos Saludables Naturales',
                    'Avenas',
                    'Endulzantes',
                    'Leches Vegetales',
                ],
                'Bebidas y Te en Polvo' => [
                    'Refrescos en Polvo',
                ],
                'Cafe' => [
                    'Café Molido',
                    'Café Soluble',
                    'Mezclas De Cafe',
                    'Cremas Para Café',
                    'Café Grano',
                ],
                'Alimento Para Bebe' => [
                    'Cereales Infantiles',
                    'Coladas',
                    'Compotas',
                ],
                'Condimentos' => [
                    'Aliños y Condimentos',
                ],
                'Enlatados y Conservas' => [
                    'Conservas Carnes',
                    'Atún y Sardinas',
                    'Conservas Vegetales',
                    'Conservas En Almibar',
                ],
                'Bases, Sopas, Cremas y Caldos' => [
                    'Proteina Vegetal',
                    'Caldos',
                    'Sopas Cremas y Bases',
                ],
                'Encurtidos' => [
                    'Encurtidos Varios',
                ],
                'Esparcibles y Margarinas' => [
                    'Margarina Cocina',
                    'Margarina Mesa',
                ],
                'Galleteria' => [
                    'Galletas Dulces',
                    'Galletas Navideñas',
                    'Galletas Saborizadas',
                    'Galletas Saladas',
                    'Galletas Saludables',
                ],
                'Harinas' => [
                    'Harina Precocida',
                    'Harinas De Trigo',
                    'Harinas Otras',
                ],
                'Leche En Polvo' => [
                    'Leche En Polvo Modificada',
                    'Leche En Polvo Tradicional',
                    'Leches Medicadas',
                ],
                'Bebidas Solubles En Leche' => [
                    'Kolas Granuladas',
                    'Malteada',
                    'Bebidas Achocolatadas',
                ],
                'Panela y Miel' => [
                    'Miel Quemada Glucosa',
                    'Panela',
                ],
                'Pastas' => [
                    'Pastas Premium',
                    'Pastas Corrientes',
                    'Pastas Especialidades',
                    'Pastas Express',
                    'Pastas Al Huevo',
                ],
                'Reposteria' => [
                    'Leche Condensada',
                    'Cremas De Leche',
                    'Frutas Deshidratadas Cristalizadas',
                    'Gelatinas En Polvo y Flanes',
                    'Mermeladas',
                    'Reposteria Otros',
                ],
                'Salsas' => [
                    'Aderezos y Pastas De Tomate',
                    'Salsas De Tomate',
                    'Mayonesa',
                    'Salsas Rosadas',
                    'Salsas Sazonadoras',
                ],
                'Cereales Para El Desayuno' => [
                    'Cereales En Barras',
                    'Cereales En Bolsa',
                    'Cereales En Caja',
                    'Granolas',
                ],
                'Chocolate Mesa' => [
                    'Chocolate Barra',
                    'Chocolate Polvo',
                ],
                'Aromáticas Te e Infusiones' => [
                    'Aromaticas',
                    'Infusiones',
                    'Te',
                ],
            ],
            'Aseo Personal' => [
                'Cuidado Capilar' => [
                    'Accesorio Para El Cabello',
                    'Acondicionador',
                    'Shampoo',
                    'Tratamientos y Cremas De Peinar',
                    'Fijadores Capilares',
                    'Tintes',
                ],
                'Cuidado De La Piel' => [
                    'Bronceadores y Bloqueadores',
                    'Cremas Corporales',
                    'Splash Corporales',
                    'Cremas Faciales',
                    'Repelentes',
                ],
                'Cuidado Del Bebe' => [
                    'Aceite Corporal',
                    'Cremas Corporales',
                    'Copitos',
                    'Crema Antipañalitis',
                    'Pañitos y Toallas',
                    'Talco',
                    'Cuidado Capilar',
                    'Kit Recién Nacido',
                    'Jabones',
                    'Colonias',
                ],
                'Higiene Desechable' => [
                    'Papel Higiénico',
                    'Protección Adulta',
                    'Protección Femenina',
                    'Pañales Niños',
                    'Pañuelos',
                ],
                'Higiene Oral' => [
                    'Accesorios De Ortodoncia',
                    'Cepillos Dentales',
                    'Crema Dental',
                    'Enjuague Bucal',
                    'Sedas e Hilos',
                ],
                'Higiene Personal' => [
                    'Talcos',
                    'Baño Liquido',
                    'Desodorantes',
                    'Estropajos',
                    'Gel Antibacterial',
                    'Jabón De Tocador Pasta',
                    'Removedores y Accesorios Uñas',
                    'Jabón De Tocador Líquido',
                ],
                'Productos De La Afeitada' => [
                    'Gel -Espumas y Loción',
                    'Cuchillas-Maquinas y Repuestos',
                ],
                'Cosmeticos' => [
                    'Accesorios',
                ],
            ],
        ];

        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($categories as $topLevelName => $subcategories) {
            $topLevel = Category::firstOrCreate([
                'slug' => Str::slug($topLevelName),
            ], [
                'name' => $topLevelName,
            ]);

            foreach ($subcategories as $subCategoryName => $items) {
                $subCategory = Category::firstOrCreate([
                    'parent_id' => $topLevel->id,
                    'slug' => Str::slug($topLevelName . ' ' . $subCategoryName),
                ], [
                    'name' => $subCategoryName,
                ]);

                foreach ($items as $itemName) {
                    Category::firstOrCreate([
                        'parent_id' => $subCategory->id,
                        'slug' => Str::slug($topLevelName . ' ' . $subCategoryName . ' ' . $itemName),
                    ], [
                        'name' => $itemName,
                    ]);
                }
            }
        }
    }
}
