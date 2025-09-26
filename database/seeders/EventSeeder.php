<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Récupérer l'utilisateur admin
        $admin = User::where('email', 'admin@ecoevent.com')->first();
        
        if (!$admin) {
            $this->command->error('Utilisateur admin non trouvé. Veuillez d\'abord exécuter le seeder AdminUserSeeder.');
            return;
        }

        $events = [
            [
                'title' => 'Nettoyage de la Plage de Casablanca',
                'short_description' => 'Rejoignez-nous pour nettoyer la plage et protéger notre environnement marin.',
                'description' => 'Participez à notre action de nettoyage de la plage de Casablanca. Nous fournirons tous les équipements nécessaires : gants, sacs poubelles, pinces. Cette action s\'inscrit dans notre démarche de protection de l\'environnement marin et de sensibilisation à la pollution plastique. Venez nombreux pour faire la différence !',
                'start_date' => Carbon::now()->addDays(7),
                'end_date' => Carbon::now()->addDays(7),
                'start_time' => '09:00',
                'end_time' => '12:00',
                'location' => 'Plage Ain Diab',
                'address' => 'Boulevard de la Corniche, Ain Diab',
                'city' => 'Casablanca',
                'postal_code' => '20000',
                'max_participants' => 50,
                'current_participants' => 0,
                'price' => 0,
                'is_free' => true,
                'is_online' => false,
                'category' => 'nettoyage',
                'eco_impact' => 'Réduction des déchets marins, protection de la faune marine, sensibilisation environnementale',
                'carbon_footprint' => 2.5,
                'sustainability_score' => 95,
                'organizer_name' => 'EcoEvent Team',
                'organizer_email' => 'admin@ecoevent.com',
                'organizer_phone' => '+212 6 12 34 56 78',
                'status' => 'active',
                'is_featured' => true,
                'registration_deadline' => Carbon::now()->addDays(5),
                'requirements' => ['Vêtements confortables', 'Chaussures fermées', 'Crème solaire'],
                'what_to_bring' => ['Bouteille d\'eau réutilisable', 'Chapeau ou casquette'],
                'accessibility_info' => 'Accessible aux personnes à mobilité réduite',
                'user_id' => $admin->id,
            ],
            [
                'title' => 'Atelier de Plantation d\'Arbres - Forêt de Bouskoura',
                'short_description' => 'Contribuez à la reforestation en plantant des arbres indigènes.',
                'description' => 'Participez à notre atelier de plantation d\'arbres dans la forêt de Bouskoura. Nous planterons des espèces indigènes adaptées au climat local. Cet événement comprend une formation sur les techniques de plantation et l\'importance de la biodiversité. Un déjeuner bio sera offert à tous les participants.',
                'start_date' => Carbon::now()->addDays(14),
                'end_date' => Carbon::now()->addDays(14),
                'start_time' => '08:00',
                'end_time' => '16:00',
                'location' => 'Forêt de Bouskoura',
                'address' => 'Route de Bouskoura, km 15',
                'city' => 'Bouskoura',
                'postal_code' => '27182',
                'max_participants' => 30,
                'current_participants' => 0,
                'price' => 50.00,
                'is_free' => false,
                'is_online' => false,
                'category' => 'plantation',
                'eco_impact' => 'Absorption de CO2, amélioration de la qualité de l\'air, préservation de la biodiversité',
                'carbon_footprint' => -15.0,
                'sustainability_score' => 90,
                'organizer_name' => 'Association Verte Maroc',
                'organizer_email' => 'contact@vertemaroc.ma',
                'organizer_phone' => '+212 5 22 33 44 55',
                'status' => 'active',
                'is_featured' => true,
                'registration_deadline' => Carbon::now()->addDays(10),
                'requirements' => ['Vêtements de travail', 'Gants de jardinage', 'Bottes'],
                'what_to_bring' => ['Déjeuner (fourni)', 'Bouteille d\'eau'],
                'accessibility_info' => 'Terrain accidenté, non accessible aux fauteuils roulants',
                'user_id' => $admin->id,
            ],
            [
                'title' => 'Conférence : Énergies Renouvelables au Maroc',
                'short_description' => 'Découvrez les dernières innovations en énergies renouvelables.',
                'description' => 'Conférence en ligne sur les énergies renouvelables au Maroc. Intervenants experts du secteur, présentation des projets solaires et éoliens, opportunités d\'investissement. Session de questions-réponses avec les participants.',
                'start_date' => Carbon::now()->addDays(21),
                'end_date' => Carbon::now()->addDays(21),
                'start_time' => '14:00',
                'end_time' => '17:00',
                'location' => null,
                'address' => null,
                'city' => 'En ligne',
                'postal_code' => null,
                'max_participants' => 200,
                'current_participants' => 0,
                'price' => 0,
                'is_free' => true,
                'is_online' => true,
                'meeting_link' => 'https://zoom.us/j/123456789',
                'category' => 'energie',
                'eco_impact' => 'Sensibilisation aux énergies propres, promotion des technologies vertes',
                'carbon_footprint' => 0.1,
                'sustainability_score' => 85,
                'organizer_name' => 'Centre de Recherche Énergétique',
                'organizer_email' => 'info@energie-maroc.ma',
                'organizer_phone' => '+212 5 37 12 34 56',
                'status' => 'active',
                'is_featured' => false,
                'registration_deadline' => Carbon::now()->addDays(19),
                'requirements' => ['Connexion internet stable', 'Application Zoom'],
                'what_to_bring' => ['Bloc-notes', 'Stylo'],
                'accessibility_info' => 'Sous-titres disponibles, interprétation en langue des signes sur demande',
                'user_id' => $admin->id,
            ],
            [
                'title' => 'Marché Bio et Local - Rabat',
                'short_description' => 'Découvrez les produits bio et locaux de nos producteurs.',
                'description' => 'Marché hebdomadaire de produits bio et locaux. Rencontrez les producteurs locaux, dégustez des produits de saison, participez à des ateliers de cuisine bio. Animations pour enfants et stands d\'information sur l\'agriculture durable.',
                'start_date' => Carbon::now()->addDays(3),
                'end_date' => Carbon::now()->addDays(3),
                'start_time' => '08:00',
                'end_time' => '14:00',
                'location' => 'Place du Marché',
                'address' => 'Avenue Mohammed V, centre-ville',
                'city' => 'Rabat',
                'postal_code' => '10000',
                'max_participants' => null,
                'current_participants' => 0,
                'price' => 0,
                'is_free' => true,
                'is_online' => false,
                'category' => 'alimentation',
                'eco_impact' => 'Promotion de l\'agriculture locale, réduction de l\'empreinte carbone alimentaire',
                'carbon_footprint' => 5.0,
                'sustainability_score' => 80,
                'organizer_name' => 'Coopérative Bio Rabat',
                'organizer_email' => 'contact@biorabat.ma',
                'organizer_phone' => '+212 5 37 98 76 54',
                'status' => 'active',
                'is_featured' => false,
                'registration_deadline' => null,
                'requirements' => null,
                'what_to_bring' => ['Sacs réutilisables', 'Monnaie'],
                'accessibility_info' => 'Accessible à tous',
                'user_id' => $admin->id,
            ],
            [
                'title' => 'Atelier Upcycling : Donnez une Seconde Vie à vos Objets',
                'short_description' => 'Apprenez à transformer vos déchets en objets utiles et décoratifs.',
                'description' => 'Atelier créatif d\'upcycling pour apprendre à transformer vos déchets du quotidien en objets utiles et décoratifs. Techniques de transformation de bouteilles plastiques, boîtes de conserve, textiles usagés. Matériel fourni, repartez avec vos créations !',
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(10),
                'start_time' => '10:00',
                'end_time' => '13:00',
                'location' => 'Centre Culturel',
                'address' => 'Rue des Arts, Quartier Gauthier',
                'city' => 'Casablanca',
                'postal_code' => '20100',
                'max_participants' => 20,
                'current_participants' => 0,
                'price' => 80.00,
                'is_free' => false,
                'is_online' => false,
                'category' => 'recyclage',
                'eco_impact' => 'Réduction des déchets, sensibilisation au recyclage créatif',
                'carbon_footprint' => 1.2,
                'sustainability_score' => 88,
                'organizer_name' => 'Atelier Créatif Verde',
                'organizer_email' => 'info@atelierverde.ma',
                'organizer_phone' => '+212 6 87 65 43 21',
                'status' => 'draft',
                'is_featured' => false,
                'registration_deadline' => Carbon::now()->addDays(8),
                'requirements' => ['Vêtements pouvant être tachés', 'Tablier (fourni)'],
                'what_to_bring' => ['Objets personnels à transformer (optionnel)'],
                'accessibility_info' => 'Accessible aux personnes à mobilité réduite',
                'user_id' => $admin->id,
            ]
        ];

        foreach ($events as $eventData) {
            Event::create($eventData);
        }

        $this->command->info('5 événements de test ont été créés avec succès !');
    }
}
