<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use App\Models\FooterContent;
use App\Models\HeaderContent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HomeContent;

class SpanishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('home_contents')->where('lang_id', 14)->delete();
DB::table('header_contents')->where('lang_id', 14)->delete();
DB::table('footer_contents')->where('lang_id', 14)->delete();

        $arr = [
            'header_title' => '¡Encuentre las mejores ofertas y ahorre en su próxima compra!',
            'header_description' => 'Obtenga comparaciones de productos gratuitas e imparciales, lea reseñas de clientes reales y',
            'header_background_img' => 'front/img/bnnr-bg.png',
            'header_img' => 'front/img/banner_image.png',
            'placeholder_text' => 'Ingresa un producto, categoría o lo que deseas comparar...',
            'trusted_brands_text' => 'Marcas confiables, opciones inmejorables',
            'trusted_brands_img' => 'front/img/marq-img1.svg',
            'most_popular' => 'Más Popular',
            'campare_business' => 'Comparar software empresarial',
            'visit_website' => 'visitar sitio web',
            'exclusive_deals' => 'Ofertas exclusivas',
            'all_exclusive' => 'Todas las ofertas exclusivas',
            'get_this_deal' => 'Obtenga esta oferta',
            'ai_section_left_img' => 'front/img/right-tool-vector1.png',
            'ai_section_right_img' => 'front/img/right-tool-vector2.png',
            'ai_title' => 'Búsqueda inteligente impulsada por IA',
            'ai_description' => 'Descubra y compare rápidamente los mejores productos con nuestra búsqueda impulsada por IA, diseñada para adaptarse a sus necesidades y preferencias específicas.',
            'ai_placeholder' => 'Ingresa un producto, categoría o lo que deseas comparar...',
            'ai_send_img' => 'front/img/btn-img.svg',
            'top_product' => 'Productos mejor valorados',
            'all_top_product' => 'Todos los productos mejor valorados',
            'latest_reviews' => 'Últimas reseñas',
            'write_review' => 'Escribe una reseña',
            'review_section_right_img' => 'front/img/right-tool-vector1.png',
            'review_section_left_img' => 'front/img/right-tool-vector2.png',
            'read_article' => 'Lee nuestros artículos',
            'view_all_article' => 'Ver todos los artículos',
            'find_tool' => 'Encuentra la herramienta adecuada',
            'find_tool_left_img' => 'front/img/right-tool-vector1.png',
            'find_tool_right_img' => 'front/img/right-tool-vector2.png',
            'user_reviews_img' => 'front/img/right-tool-img1.png',
            'verify_user_review' => 'Reseñas de usuarios verificadas',
            'verify_review_description' => 'Lee comentarios reales de usuarios verificados para ayudarte a tomar la decisión correcta.',
            'price_compare_img' => 'front/img/right-tool-img2.png',
            'feature_price' => 'Comparaciones de características y precios',
            'feature_price_description' => 'Compara fácilmente software basado en características clave, precios y calificaciones de usuarios.',
            'independent_img' => 'front/img/right-tool-img3.png',
            'independent' => 'Perspectivas independientes',
            'independent_description' => 'Accede a investigaciones imparciales y basadas en datos para obtener el mayor valor de tu software.',
            'get_button_lable' => 'Comenzar'
        ];

        foreach($arr as $meta_key=> $meta_value){
            HomeContent::create([
                'meta_key' => $meta_key,
                'meta_value' => $meta_value,
                'lang_id' => '14'
            ]);
        }

        $headercontent = [
            'header_logo' => 'front/img/logo.svg',
            'header_search_placeholder' => 'Ingresa un producto, categoría o lo que te gustaría comparar...',
            'login_btn_lable' => 'Iniciar sesión',
            'sign_up_btn_lable' => 'Registrarse',
            'sign_out_btn_lable' => 'Cerrar sesión',
            'exclusive' => 'Exclusivo',
            'categories' => 'Categorías',
            'top_rated_product' => 'Producto más valorado',
            'expert_guide' => 'Guía de expertos',
            'help_center' => 'Centro de ayuda',
        ];

        $footercontent = [
            'footer_logo' => 'front/img/logo.svg',
            'facebook_icon' => 'header_logo/1734508523_facebook.svg',
            'instagram_icon' => 'header_logo/1734508559_Instagram.svg',
            'twitter_icon' => 'header_logo/1734508611_Twitter.svg',
            'facebook_url' => 'https://www.facebook.com/login/',
            'twitter_url' => 'https://www.instagram.com/accounts/login/',
            'discover' => 'Descubrir',
            'categories' => 'categorias',
            'top_rated_product' => 'Productos más valorados',
            'exclusive_deal' => 'Ofertas exclusivas',
            'company' => 'Empresa',
            'who_we_are' => 'Quiénes somos',
            'privacy_policy' => 'Política de privacidad',
            'terms_and_conditions' => 'Términos y condiciones',
            'vendors' => 'Proveedores',
            'get_listed' => 'Regístrate',
            // 'vendor_login' => 'Iniciar sesión como proveedor',
            'help' => 'Ayuda',
            'expert_guides' => 'Guías de expertos',
            'help_center' => 'Centro de ayuda',
            'contact' => 'Contacto',
            'follow_us' => 'Síguenos',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'twitter' => 'Twitter',
        ];


        foreach($headercontent as $meta_key=> $meta_value){
            HeaderContent::create([
                'meta_key' => $meta_key,
                'meta_value' => $meta_value,
                'lang_id' => '18'
            ]);
        }

        foreach($footercontent as $meta_key=> $meta_value){
            FooterContent::create([
                'meta_key' => $meta_key,
                'meta_value' => $meta_value,
                'lang_id' => '18'
            ]);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
