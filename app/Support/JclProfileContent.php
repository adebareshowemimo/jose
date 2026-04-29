<?php

namespace App\Support;

class JclProfileContent
{
    public static function company(): array
    {
        return [
            'name' => 'Jose Consulting Limited (JCL)',
            'short_name' => 'JCL',
            'tagline' => 'Empowering skills. Connecting worlds.',
            'summary' => 'JCL is a workforce development company driving job creation in the maritime and energy sector. We build employability through global standard trainings, clear career pathways and direct access to industry opportunities.',
            'summary_long' => 'Jose Consulting Limited helps individuals and organizations strengthen employability, workforce readiness, and global opportunity pathways across the maritime/Logistics and energy sectors.',
            'hero' => [
                'eyebrow' => 'Maritime and energy workforce transformation',
                'headline' => 'LEAPING FORWARD INTO OPPORTUNITIES',
                'description' => '',
                'primary_cta' => [
                    'label' => 'Start your pathway',
                    'route' => 'auth.register',
                ],
                'secondary_cta' => [
                    'label' => 'Contact JCL',
                    'route' => 'contact.index',
                ],
            ],
            'vision' => [
                'statement' => 'To be a pace setter of job creation in the maritime and energy sector.',
                'pillars' => [
                    'Maritime and energy professionals are globally competitive and locally impactful.',
                    'Employability skills are accessible, practical, and industry-recognized.',
                    'Innovation and partnerships drive sustainable growth across industries.',
                    'Talent mobility connects Nigeria and Africa to international opportunities, strengthening global collaboration.',
                ],
            ],
            'mission' => [
                'statement' => 'We create jobs by building employability — training people to international standards, connecting them to employers, and supporting the infrastructure that drives maritime and energy growth.',
                // Updated per client brief: same statement maintained
                'actions' => [
                    'Building employability skills through industry-recognized qualifications.',
                    'Providing practical, hands-on training that meets international standards.',
                    'Connecting talent with opportunities through a national maritime database and skills exchange platform.',
                    'Partnering with global experts to deliver specialized programs in logistics, safety, and energy operations.',
                    'Driving innovation and growth through creative and pragmatic strategies.',
                ],
            ],
            'values' => [
                [
                    'name' => 'Excellence',
                    'icon' => 'lucide:award',
                    'description' => 'We deliver world-class consulting and workforce solutions that meet international standards.',
                ],
                [
                    'name' => 'Empowerment',
                    'icon' => 'lucide:rocket',
                    'description' => 'We unlock potential by equipping individuals with the skills, confidence, and pathways to succeed.',
                ],
                [
                    'name' => 'Innovation',
                    'icon' => 'lucide:lightbulb',
                    'description' => 'We embrace creativity and pragmatic thinking to design programs that respond to evolving industry needs.',
                ],
                [
                    'name' => 'Integrity',
                    'icon' => 'lucide:shield-check',
                    'description' => 'We build trust through transparency, professionalism, and ethical practices in every partnership.',
                ],
                [
                    'name' => 'Global Competitiveness',
                    'icon' => 'lucide:globe-2',
                    'description' => 'We prepare professionals to thrive locally and internationally while meeting the highest industry benchmarks.',
                ],
                [
                    'name' => 'Collaboration',
                    'icon' => 'lucide:handshake',
                    'description' => 'We work hand-in-hand with global partners, industry experts, and organizations to create impactful opportunities.',
                ],
            ],
            'leadership' => [
                [
                    'name' => 'Uju Obi',
                    'title' => 'CEO & Project Director',
                    'category' => 'Executive leadership',
                    'summary' => 'Uju brings more than seven years of banking experience alongside leadership in one of Nigeria’s leading oil and gas trading companies, combining financial discipline with deep operational insight.',
                    'highlights' => [
                        'Advanced training in international oil and gas, tanker vessel ownership, overseas operations, and ship management.',
                        'Leads JCL with a focus on strategic growth, employability, and global competitiveness.',
                    ],
                ],
                [
                    'name' => 'Chukwuma Nduche',
                    'title' => 'Project Director',
                    'category' => 'Brand and strategic positioning',
                    'summary' => 'Chukwuma is a brand management specialist with over 16 years of experience spanning FMCGs, telecommunications, and oil & gas across Nigeria and South Africa.',
                    'highlights' => [
                        'Formerly worked with JWT Nigeria and JWT’s African Regional Group in South Africa.',
                        'Member of APCON and IAA with advanced training in brand building, total quality management, and global brand communication.',
                    ],
                ],
                [
                    'name' => 'Tim',
                    'title' => 'Oil & Gas Specialist',
                    'category' => 'Energy operations',
                    'summary' => 'Tim has served as Depot and Operations Manager and Oilfield Processing & Inspection Engineer, bringing technical depth in logistics, inspection, and energy operations.',
                    'highlights' => [
                        'International training in Tulsa, Paris, Tokyo, and with EDF Energy & Scottish and Southern Energy.',
                        'Strengthens JCL energy programs with practical expertise in pipeline operations and crude logistics.',
                    ],
                ],
                [
                    'name' => 'Abel Eriamiantoe Ehioghae',
                    'title' => 'Logistics & Distribution Expert',
                    'category' => 'Logistics and supply chain',
                    'summary' => 'Abel is a seasoned logistics and distribution expert with consulting experience for NAOC, AGIP, TEXACO, and NNPC, plus hands-on operational exposure in the UK.',
                    'highlights' => [
                        'Studied transport at the University of London and gained field experience at the Port of Tilbury and Heathrow freight department.',
                        'Member of the Institute of Motor Industries (UK) and the Engineering Registration Board (UK).',
                    ],
                ],
                [
                    'name' => 'Graham Freeman',
                    'title' => 'Technical Partner / Consultant',
                    'category' => 'Safety and security training',
                    'summary' => 'Graham is an internationally certified safety expert trained under NEBOSH UK and a City & Guilds certified security trainer.',
                    'highlights' => [
                        'Certifications include C&G 7304, C&G 1886, BS 7858, and BG Level 3.',
                        'Adds internationally benchmarked safety capability to JCL delivery teams.',
                    ],
                ],
                [
                    'name' => 'John Flyn',
                    'title' => 'Technical Partner / Consultant',
                    'category' => 'Audit and compliance',
                    'summary' => 'John is an NEBOSH UK-trained safety expert and internationally registered Lead Auditor across ISO 9001, OHSAS 1801, and ISO 1401 frameworks.',
                    'highlights' => [
                        'British Standards Institute-certified senior trainer and consultant.',
                        'Supports JCL with audit-readiness, training quality, and operational compliance expertise.',
                    ],
                ],
            ],
            'partnerships' => [
                [
                    'name' => 'Aberdeen Team',
                    'lead' => 'Led by David Evans',
                    'focus' => 'Maritime, supply and trading, ports, and logistics training.',
                    'strength' => 'Provides specialist international delivery support for maritime and logistics capability building.',
                ],
                [
                    'name' => 'Dubai Team',
                    'lead' => 'Regional technical specialists',
                    'focus' => 'Pipe maintenance, welding, and fitting technology.',
                    'strength' => 'Extends JCL’s ability to deploy specialized technical programs for industrial and energy operations.',
                ],
            ],
            'expertise' => [
                'Workforce development in maritime and energy sectors.',
                'Hands-on training aligned to international standards.',
                'Employability and retention strategies for organizations.',
                'Logistics, safety, energy operations, and talent mobility pathways.',
            ],
            'edge' => [
                'Workforce development focused on maritime and energy sectors — building employability for global competitiveness.',
                'Global expertise through internationally trained leadership and partners.',
                'Practical mobilization of expert training teams — almost immediately.',
                'Innovation through creative and pragmatic delivery strategies.',
            ],
            'stats' => [
                [
                    'value' => '2 Sectors',
                    'label' => 'Maritime/Logistics',
                    'description' => 'Recruitment, crew management, ship chandelling, and logistics workforce development.',
                ],
                [
                    'value' => 'End-to-End',
                    'label' => 'Energy Workforce Development',
                    'description' => 'Training, consulting, and career pathways for the energy sector.',
                ],
            ],
            'journey' => [
                [
                    'title' => 'Assess workforce needs',
                    'description' => 'Clarify the capability gaps, readiness targets, or employability outcomes your organization or talent group needs to solve.',
                ],
                [
                    'title' => 'Validate readiness',
                    'description' => 'Strengthen confidence with qualifications, compliance alignment, and industry-recognized skill development.',
                ],
                [
                    'title' => 'Connect to opportunity',
                    'description' => 'Link qualified talent and organizations to career pathways, partnerships, and global opportunities.',
                ],
                [
                    'title' => 'Deliver practical training',
                    'description' => 'Run programs designed around international standards, operational realities, and employer expectations.',
                ],
            ],
            'contact_pathways' => [
                [
                    'title' => 'Candidate Pathway',
                    'description' => 'Prospective professionals can register, browse opportunities, and use the platform as a gateway into future readiness.',
                    'icon' => 'lucide:user-check',
                ],
                [
                    'title' => 'Partnership Discussions',
                    'description' => 'Reach out if you want to collaborate on maritime, energy, logistics, safety, or specialist delivery initiatives.',
                    'icon' => 'lucide:handshake',
                ],
                [
                    'title' => 'Services',
                    'description' => 'Engage JCL for crew management, ship chandelling, marine procurement, marine insurance, travel management, and more.',
                    'icon' => 'lucide:briefcase',
                ],
                [
                    'title' => 'Trainings & Consulting Enquiries',
                    'description' => 'Use the contact form to discuss workforce development programs, technical training, or operational consulting support.',
                    'icon' => 'lucide:graduation-cap',
                ],
            ],
            'final_cta' => [
                'headline' => 'Ready to connect and deliver globally competitive talents?',
                'description' => 'Whether you are developing people, strengthening workforce retention, or opening access to new opportunities, JCL can help you move from strategy to execution.',
                'primary' => [
                    'label' => 'Contact JCL',
                    'route' => 'contact.index',
                ],
                'secondary' => [
                    'label' => 'Browse Jobs',
                    'route' => 'job.index',
                ],
            ],

            'training_programs' => [
                [
                    'title' => 'STCW Basic Safety Training',
                    'category' => 'Certification',
                    'icon' => 'lucide:shield-check',
                    'duration' => '5 days',
                    'mode' => 'In-person',
                    'description' => 'IMO-compliant Standards of Training, Certification, and Watchkeeping covering personal survival, fire prevention, first aid, and personal safety & social responsibility.',
                ],
                [
                    'title' => 'Offshore Safety & Emergency Response (BOSIET)',
                    'category' => 'Safety',
                    'icon' => 'lucide:life-buoy',
                    'duration' => '4 days',
                    'mode' => 'In-person',
                    'description' => 'Basic Offshore Safety Induction and Emergency Training for personnel working on offshore installations, covering helicopter underwater escape, sea survival, and firefighting.',
                ],
                [
                    'title' => 'Maritime Leadership & Crew Resource Management',
                    'category' => 'Leadership',
                    'icon' => 'lucide:compass',
                    'duration' => '3 days',
                    'mode' => 'Hybrid',
                    'description' => 'Developing bridge team management, situational awareness, and decision-making skills for senior officers and shore-based operational managers.',
                ],
                [
                    'title' => 'Port & Terminal Operations Management',
                    'category' => 'Operations',
                    'icon' => 'lucide:container',
                    'duration' => '5 days',
                    'mode' => 'In-person',
                    'description' => 'End-to-end port operations covering berth planning, cargo handling, terminal productivity, safety compliance, and logistics chain management.',
                ],
                [
                    'title' => 'NEBOSH International General Certificate',
                    'category' => 'Certification',
                    'icon' => 'lucide:badge-check',
                    'duration' => '10 days',
                    'mode' => 'Hybrid',
                    'description' => 'Internationally recognized health and safety qualification covering workplace hazard management, risk assessment, and regulatory compliance across industries.',
                ],
                [
                    'title' => 'Energy Sector Workforce Readiness',
                    'category' => 'Career Pathway',
                    'icon' => 'lucide:zap',
                    'duration' => '2 weeks',
                    'mode' => 'Hybrid',
                    'description' => 'Comprehensive preparation for entry into oil & gas, renewable energy, and power sector roles — covering technical fundamentals, safety culture, and employability skills.',
                ],
            ],

            'events' => [
                [
                    'title' => 'JCL Maritime Workforce Symposium 2026',
                    'type' => 'Conference',
                    'date' => 'June 18 – 20, 2026',
                    'location' => 'Lagos, Nigeria',
                    'description' => 'JCL\'s flagship annual event bringing together maritime employers, training providers, and policymakers to discuss workforce transformation, talent mobility, and certification frameworks for West Africa.',
                    'status' => 'upcoming',
                ],
                [
                    'title' => 'Offshore Safety Masterclass Series',
                    'type' => 'Seminar',
                    'date' => 'July 10 – 11, 2026',
                    'location' => 'Lagos, Nigeria',
                    'description' => 'A two-day intensive seminar led by NEBOSH-certified trainers covering risk assessment, incident investigation, and safety management systems for offshore and onshore energy operations.',
                    'status' => 'upcoming',
                ],
                [
                    'title' => 'STCW Refresher & Competency Assessment Week',
                    'type' => 'Training Event',
                    'date' => 'August 5 – 9, 2026',
                    'location' => 'Lagos, Nigeria',
                    'description' => 'Intensive refresher training and competency assessments for seafarers needing to renew or validate their STCW certifications, with practical simulation exercises.',
                    'status' => 'upcoming',
                ],
                [
                    'title' => 'Energy Transition & Maritime Innovation Summit',
                    'type' => 'Conference',
                    'date' => 'October 14 – 16, 2026',
                    'location' => 'Dubai, UAE',
                    'description' => 'Co-hosted by JCL\'s Dubai partner team, exploring how decarbonization, digitalization, and workforce reskilling are reshaping the maritime and energy landscape.',
                    'status' => 'upcoming',
                ],
                [
                    'title' => 'West Africa Port Safety & Operations Forum',
                    'type' => 'Seminar',
                    'date' => 'November 21 – 22, 2026',
                    'location' => 'Lagos, Nigeria',
                    'description' => 'Focused forum on port safety protocols, terminal efficiency, and compliance standards for port authorities and logistics operators across the West Africa corridor.',
                    'status' => 'upcoming',
                ],
                [
                    'title' => 'JCL Annual Training Awards & Graduation',
                    'type' => 'Event',
                    'date' => 'December 12, 2026',
                    'location' => 'Lagos, Nigeria',
                    'description' => 'Celebrating the achievements of JCL-trained professionals who have completed certification programs throughout the year, with keynotes from industry leaders.',
                    'status' => 'upcoming',
                ],
            ],

            'industry_events' => [
                [
                    'title' => 'Posidonia 2026',
                    'date' => 'Jun 1 – 5, 2026',
                    'location' => 'Athens, Greece',
                    'description' => 'The world\'s most prestigious maritime event, attracting global shipping leaders.',
                ],
                [
                    'title' => 'SMM 2026',
                    'date' => 'Sep 1 – 4, 2026',
                    'location' => 'Hamburg, Germany',
                    'description' => 'Leading international maritime trade fair — shipbuilding, marine technology, and services.',
                ],
                [
                    'title' => 'Crew Connect Global 2026',
                    'date' => 'Oct 20 – 22, 2026',
                    'location' => 'Manila, Philippines',
                    'description' => 'The world\'s premier marine crewing conference on talent, retention, and crew welfare.',
                ],
                [
                    'title' => 'Seatrade Maritime Qatar 2026',
                    'date' => 'Dec 8 – 9, 2026',
                    'location' => 'Doha, Qatar',
                    'description' => 'Gateway to Qatar\'s ports, logistics, and maritime supply chain sector.',
                ],
            ],
        ];
    }

    /**
     * Curated image URLs for brochure pages.
     */
    public static function images(): array
    {
        return [
            // Hero & banner backgrounds
            'hero_aerial_cargo'  => asset('images/premium/harbor-aerial.jpg'),

            // Maritime operations
            'safety_officer'     => asset('images/premium/safety-officer.jpg'),
            'deck_officer'       => asset('images/premium/deck-officer.jpg'),
            'sailor_repairs'     => asset('images/premium/sailor-repairs.jpg'),

            // Container / logistics
            'container_port'     => asset('images/premium/container-port.jpg'),
            'cargo_colorful'     => asset('images/premium/ship-chandelling.jpg'),
            'aerial_container'   => asset('images/premium/harbor-aerial.jpg'),

            // Offshore vessels & rigs
            'offshore_vessel'    => asset('images/premium/offshore-vessel.jpg'),

            // Professional / Consulting
            'business_meeting'   => asset('images/premium/site-officer.jpg'),

            // Training & workforce
            'maritime_training'  => asset('images/premium/safety-officer.jpg'),

            // Local hero images
            'home_1'             => asset('images/home 1.jpg'),
            'home_2'             => asset('images/home 2.jpg'),
            'home_3'             => asset('images/home 3.jpg'),

            // Page-specific local images
            'about_page'         => asset('images/About page image.jpeg'),
            'crew_management'    => asset('images/premium/crew-management.jpg'),
            'ship_chandelling'   => asset('images/premium/ship-chandelling.jpg'),
            'marine_procurement' => asset('images/premium/marine-procurement.jpg'),
            'events_hero'        => asset('images/premium/events-hero.jpg'),
            'contact_hero'       => asset('images/premium/contact-hero.jpg'),
            'career_banner'      => asset('images/premium/career-banner.jpg'),
            'news_marine'        => asset('images/news marine.jpg'),
            'auth_bg'            => asset('images/sing in or register.jpg'),
        ];
    }
}
