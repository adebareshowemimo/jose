<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    public function test_jcl_homepage_renders_successfully(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSeeText('Jose Consulting Limited (JCL)')
            ->assertSeeText('World-class training, consulting, and career pathways')
            ->assertSeeText('Workforce transformation');
    }

    public function test_about_page_renders_jcl_story_content(): void
    {
        $response = $this->get(route('about.index'));

        $response
            ->assertOk()
            ->assertSeeText('About JCL')
            ->assertSeeText('Who we are')
            ->assertSeeText('Why organizations and professionals choose JCL');
    }

    public function test_leadership_page_renders_key_experts(): void
    {
        $response = $this->get(route('leadership.index'));

        $response
            ->assertOk()
            ->assertSeeText('Leadership & Experts')
            ->assertSeeText('Uju Obi')
            ->assertSeeText('Chukwuma Nduche')
            ->assertSeeText('Graham Freeman');
    }

    public function test_partnerships_page_renders_global_collaboration_content(): void
    {
        $response = $this->get(route('partnerships.index'));

        $response
            ->assertOk()
            ->assertSeeText('Partnerships & Expertise')
            ->assertSeeText('Aberdeen Team')
            ->assertSeeText('Dubai Team')
            ->assertSeeText('mobilize specialized training teams within two weeks');
    }

    public function test_contact_page_renders_jcl_enquiry_paths(): void
    {
        $response = $this->get(route('contact.index'));

        $response
            ->assertOk()
            ->assertSeeText('Contact JCL')
            ->assertSeeText('Training & consulting enquiries')
            ->assertSeeText('Best way to reach JCL');
    }
}
