<?php

namespace Database\Seeders;

use App\Models\TrainingProgram;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TrainingProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            [
                'title' => 'STCW Basic Safety Training',
                'category' => 'Certification',
                'level' => 'Foundation',
                'duration' => '5 days',
                'price' => 450000,
                'short_description' => 'IMO-compliant Standards of Training, Certification, and Watchkeeping covering personal survival, fire prevention, first aid, and personal safety & social responsibility.',
                'long_description' => <<<'HTML'
<p>The <strong>STCW Basic Safety Training</strong> is the international entry-level certification required of every seafarer working on commercial ships under SOLAS regulations.</p>
<h2>Course modules</h2>
<p>Over five intensive days you cover four core IMO-mandated modules:</p>
<ul>
  <li><strong>Personal Survival Techniques (PST)</strong> — life-jacket use, life raft launch, sea survival drills</li>
  <li><strong>Fire Prevention and Fire Fighting (FPFF)</strong> — shipboard fire types, extinguisher classes, BA equipment</li>
  <li><strong>Elementary First Aid (EFA)</strong> — primary survey, CPR, common shipboard injuries</li>
  <li><strong>Personal Safety and Social Responsibilities (PSSR)</strong> — emergency procedures, pollution prevention, effective communication on board</li>
</ul>
<p>Delivered by IMO-accredited instructors with practical wet-drill and hot-fire components. On successful completion you receive an STCW certificate recognised by all flag states.</p>
HTML,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Offshore Safety & Emergency Response (BOSIET)',
                'category' => 'Safety',
                'level' => 'Foundation',
                'duration' => '4 days',
                'price' => 950000,
                'short_description' => 'OPITO Basic Offshore Safety Induction and Emergency Training for personnel working on offshore installations — helicopter underwater escape, sea survival, and firefighting.',
                'long_description' => <<<'HTML'
<p><strong>BOSIET</strong> is the OPITO-accredited certification required by every individual travelling offshore for the first time, or returning after a four-year break.</p>
<h2>What the four days cover</h2>
<ul>
  <li><strong>Safety induction</strong> — offshore hazards, alarms, muster procedures</li>
  <li><strong>Helicopter safety and escape (HUET)</strong> — including the helicopter underwater escape simulator</li>
  <li><strong>Sea survival and first aid</strong> — life-raft drills, sea survival techniques, basic first aid</li>
  <li><strong>Firefighting and self-rescue</strong> — practical firefighting in compartmentalised environments using SCBA</li>
</ul>
<p>Delivered at our offshore safety centre with full HUET pool, fire ground, and life-raft tank. On completion you receive the OPITO BOSIET certificate, accepted by every major operator worldwide.</p>
HTML,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Maritime Leadership & Crew Resource Management',
                'category' => 'Leadership',
                'level' => 'Advanced',
                'duration' => '3 days',
                'price' => 650000,
                'short_description' => 'Bridge team management, situational awareness, and decision-making skills for senior officers and shore-based operational managers.',
                'long_description' => <<<'HTML'
<p>Designed for <strong>Chief Officers, Masters, and shore-based operations managers</strong>, this STCW-aligned course builds the human-factors competencies that prevent the majority of maritime incidents.</p>
<h2>Three days of facilitated learning</h2>
<ul>
  <li>Bridge team management and the BTM model</li>
  <li>Situational awareness — the OODA loop applied to vessel operations</li>
  <li>Decision-making under uncertainty (recognition-primed and analytical)</li>
  <li>Communication, assertiveness and challenge-and-response</li>
  <li>Workload management and cognitive bias</li>
  <li>Leadership styles and team dynamics on a multi-cultural crew</li>
</ul>
<p>Includes simulator scenarios and structured debriefs. Completion contributes to <strong>STCW Reg II/2 and III/2</strong> leadership requirements.</p>
HTML,
                'sort_order' => 3,
            ],
            [
                'title' => 'Port & Terminal Operations Management',
                'category' => 'Operations',
                'level' => 'Intermediate',
                'duration' => '5 days',
                'price' => 750000,
                'short_description' => 'End-to-end port operations covering berth planning, cargo handling, terminal productivity, safety compliance, and logistics chain management.',
                'long_description' => <<<'HTML'
<p>A comprehensive five-day programme for <strong>port supervisors, terminal operators, and logistics managers</strong> covering the full operational lifecycle of a modern container or bulk terminal.</p>
<h2>Curriculum</h2>
<ul>
  <li>Port and terminal types — container, bulk, RoRo, oil and LNG</li>
  <li>Berth planning and vessel scheduling</li>
  <li>Cargo handling equipment and operational productivity metrics (TEU/hour, BMPH)</li>
  <li>Yard planning and stack management</li>
  <li>Terminal Operating Systems (TOS) overview</li>
  <li>Health, safety and environment in port operations</li>
  <li>ISPS Code and port security</li>
  <li>Multimodal logistics and the landside interface</li>
</ul>
<p>Includes a port site visit and a terminal-management table-top exercise.</p>
HTML,
                'sort_order' => 4,
            ],
            [
                'title' => 'NEBOSH International General Certificate',
                'category' => 'Certification',
                'level' => 'Intermediate',
                'duration' => '10 days',
                'price' => 1200000,
                'short_description' => 'Internationally recognised health and safety qualification covering workplace hazard management, risk assessment, and regulatory compliance across industries.',
                'long_description' => <<<'HTML'
<p>The <strong>NEBOSH International General Certificate (IGC)</strong> is the world's most popular health-and-safety qualification — held by over 200,000 professionals globally and the entry-point to a career in HSE.</p>
<h2>What you'll be examined on</h2>
<p>The ten-day programme prepares you for the two NEBOSH IGC unit examinations:</p>
<ul>
  <li><strong>Unit IG1: Management of Health and Safety</strong> — leadership, risk assessment, performance measurement</li>
  <li><strong>Unit IG2: Practical Risk Assessment</strong> — workplace observation, control hierarchy, action planning</li>
</ul>
<h2>Topics covered</h2>
<p>Physical, chemical, biological, psychological and ergonomic hazards; fire safety; work equipment; manual handling; transport safety; construction and excavation hazards.</p>
<p>Delivered by NEBOSH-registered tutors. The IGC is internationally recognised and recommended by the <strong>Institution of Occupational Safety and Health (IOSH)</strong> for technician membership.</p>
HTML,
                'is_featured' => true,
                'sort_order' => 5,
            ],
            [
                'title' => 'Energy Sector Workforce Readiness',
                'category' => 'Career Pathway',
                'level' => 'Foundation',
                'duration' => '2 weeks',
                'price' => 550000,
                'short_description' => 'Comprehensive preparation for entry into oil & gas, renewable energy, and power sector roles — technical fundamentals, safety culture, and employability skills.',
                'long_description' => <<<'HTML'
<p>A two-week intensive readiness programme for <strong>graduates and career-changers</strong> entering the oil-and-gas, renewables, or power sector for the first time.</p>
<h2>Week one — sector fundamentals</h2>
<ul>
  <li>Upstream, midstream and downstream value chain</li>
  <li>Renewable energy generation (wind, solar, geothermal, marine)</li>
  <li>Power generation, transmission and distribution</li>
  <li>HSE culture and behaviour-based safety</li>
  <li>Asset integrity and process safety basics</li>
</ul>
<h2>Week two — workplace readiness</h2>
<ul>
  <li>Industry CV and competency-based interview preparation</li>
  <li>Communication and reporting in technical environments</li>
  <li>Permit-to-work systems</li>
  <li>Working in cross-cultural and shift-work environments</li>
  <li>A practical mock recruitment day with sector employers</li>
</ul>
<p>Graduates of this programme are placed into <strong>JCL's preferred-candidate pipeline</strong> for our employer network.</p>
HTML,
                'sort_order' => 6,
            ],
        ];

        foreach ($programs as $row) {
            $row['type'] = TrainingProgram::TYPE_TRAINING;
            $row['currency'] = 'NGN';
            $row['is_active'] = true;
            $row['slug'] = Str::slug($row['title']);

            TrainingProgram::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }
}
