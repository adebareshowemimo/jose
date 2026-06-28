<?php

namespace App\Support;

/**
 * Authoritative source for the JoseOceanJobs legal pages.
 *
 * Content is transcribed verbatim from the business-supplied documents:
 *   - JOSEOCEANJOBS_TERMS_OF_SERVICE_v2.docx  -> termsOfService()
 *   - JCL KEY TERMS DEFINITION.docx           -> definitions()
 *
 * Sections are modelled as block lists so the Blade view can render the body
 * and the section navigation (table of contents) from a single source.
 */
class JclLegalContent
{
    /**
     * Terms of Service — meta header + 31 ordered sections.
     */
    public static function termsOfService(): array
    {
        return [
            'meta' => [
                'effective' => 'Effective Date: 2026',
                'version' => 'Version 1.0',
                'site' => 'joseoceanjobs.com',
            ],
            'sections' => [
                [
                    'id' => 'introduction',
                    'number' => '1',
                    'title' => 'Introduction',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'Welcome to JoseOceanJobs, a platform owned and operated by Jose Consulting Limited (JCL), a company registered in Nigeria. JoseOceanJobs is a maritime and energy workforce transformation platform that connects job seekers, employers, and organisations with opportunities, training, and services across the maritime and energy sectors.'],
                        ['type' => 'p', 'text' => 'By accessing or using joseoceanjobs.com (the “Platform”), registering an account, submitting an application, enrolling in a training programme, or requesting any service, you agree to be bound by these Terms of Service (“Terms”). Please read them carefully before using the Platform.'],
                        ['type' => 'p', 'text' => 'If you do not agree to these Terms, you must not use the Platform.'],
                    ],
                ],
                [
                    'id' => 'who-these-terms-apply-to',
                    'number' => '2',
                    'title' => 'Who These Terms Apply To',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'These Terms apply to all users of the Platform, including:'],
                        ['type' => 'ul', 'items' => [
                            'Job Seekers and Candidates — individuals registering to browse jobs, apply for positions, access career pathways, or enrol in training and certification programmes',
                            'Employers and Organisations — companies, vessel operators, oil & gas firms, and other organisations posting jobs, sourcing talent, or requesting workforce solutions',
                            'Training Participants — individuals enrolling in soft skills, technical, maritime certification, or professional development programmes offered through the Platform',
                            'Marine and Business Service Clients — organisations requesting services including crew management, ship chandelling, marine procurement, marine insurance, marine travel management, contract staffing, mobilisation services, or global opportunity services',
                            'Visitors — anyone accessing the Platform without registering',
                        ]],
                        ['type' => 'p', 'text' => 'Where specific terms apply to a particular category of user, this will be clearly stated.'],
                    ],
                ],
                [
                    'id' => 'the-platform-and-our-role',
                    'number' => '3',
                    'title' => 'The Platform and Our Role',
                    'blocks' => [
                        ['type' => 'h3', 'text' => '3.1 What JoseOceanJobs Does'],
                        ['type' => 'p', 'text' => 'JoseOceanJobs provides a platform and service delivery infrastructure that:'],
                        ['type' => 'ul', 'items' => [
                            'Lists job opportunities across the maritime and energy sectors',
                            'Connects qualified candidates with employers and vessel operators',
                            'Provides access to training and professional certification programmes',
                            'Facilitates marine and business services through a network of vetted partners',
                            'Supports career pathway development through internship and apprenticeship programmes',
                            'Offers workforce development consulting and contract staffing solutions',
                        ]],
                        ['type' => 'h3', 'text' => '3.2 Partnership-Based Delivery'],
                        ['type' => 'p', 'text' => 'Many services offered through the Platform are delivered in partnership with third-party providers. In all cases, JoseOceanJobs acts as the primary point of contact and client-facing entity. Partner identities are not disclosed to clients. JCL coordinates the delivery of services through its vetted partner network and maintains standards of service coordination, but does not guarantee the performance, output, or conduct of third-party delivery partners. Responsibility for actual service delivery rests with the relevant partner.'],
                        ['type' => 'h3', 'text' => '3.3 No Guarantee of Employment'],
                        ['type' => 'p', 'text' => 'JoseOceanJobs facilitates connections between candidates and employers but does not guarantee employment, placement, or any specific outcome from using the Platform. Hiring decisions rest solely with the employer or organisation.'],
                    ],
                ],
                [
                    'id' => 'account-registration',
                    'number' => '4',
                    'title' => 'Account Registration',
                    'blocks' => [
                        ['type' => 'h3', 'text' => '4.1 Eligibility'],
                        ['type' => 'p', 'text' => 'To register on the Platform, you must:'],
                        ['type' => 'ul', 'items' => [
                            'Be at least 18 years of age',
                            'Provide accurate, current, and complete information during registration',
                            'Have the legal capacity to enter into a binding agreement',
                            'Not have been previously suspended or removed from the Platform',
                        ]],
                        ['type' => 'h3', 'text' => '4.2 Account Responsibility'],
                        ['type' => 'p', 'text' => 'You are responsible for maintaining the confidentiality of your account credentials. You agree to:'],
                        ['type' => 'ul', 'items' => [
                            'Not share your login details with any other person',
                            'Notify JCL immediately at info@joseoceanjobs.com if you suspect unauthorised access to your account',
                            'Accept responsibility for all activity that occurs under your account',
                        ]],
                        ['type' => 'h3', 'text' => '4.3 Accurate Information'],
                        ['type' => 'p', 'text' => 'You must ensure all information provided on your profile, CV, applications, or service requests is truthful and accurate. JCL reserves the right to suspend or remove accounts where false or misleading information is provided.'],
                    ],
                ],
                [
                    'id' => 'job-seekers-and-candidates',
                    'number' => '5',
                    'title' => 'Job Seekers and Candidates',
                    'blocks' => [
                        ['type' => 'h3', 'text' => '5.1 Profile and Applications'],
                        ['type' => 'p', 'text' => 'As a job seeker, you may create a profile, upload your CV, and apply for positions listed on the Platform. By doing so, you:'],
                        ['type' => 'ul', 'items' => [
                            'Grant JCL permission to share your profile and application with the relevant employer or crewing partner',
                            'Confirm that all qualifications, certifications, and experience stated are genuine and verifiable',
                            'Acknowledge that submission of an application does not guarantee an interview or placement',
                        ]],
                        ['type' => 'h3', 'text' => '5.2 Certifications and Documentation'],
                        ['type' => 'p', 'text' => 'For roles requiring maritime certifications (such as STCW, BOSIET, HUET, or OPITO qualifications), you are solely responsible for ensuring your certificates are valid, current, and authentic. JCL may verify documentation and reserves the right to remove applications where certifications cannot be confirmed.'],
                        ['type' => 'h3', 'text' => '5.3 Employability Requirement'],
                        ['type' => 'p', 'text' => 'All candidates selected for placement through JoseOceanJobs must either hold a recognised employability skills qualification or successfully complete a JoseOceanJobs-approved employability training programme prior to deployment. This requirement applies to all roles across the maritime and energy sectors.'],
                        ['type' => 'ul', 'items' => [
                            'Candidates who do not already hold an employability qualification will be directed to complete the JoseOceanJobs Employability Training Programme before their application progresses to the employer shortlisting stage',
                            'Employability training covers professional workplace conduct, communication skills, maritime and energy sector awareness, CV and interview preparation, and workplace safety standards',
                            'JCL reserves the right to withhold or withdraw a candidate from any shortlist or placement process if the employability requirement has not been satisfied',
                        ]],
                        ['type' => 'h3', 'text' => '5.4 Training Enrolments'],
                        ['type' => 'p', 'text' => 'When enrolling in a training programme through the Platform:'],
                        ['type' => 'ul', 'items' => [
                            'You confirm your eligibility for the programme as stated in the course requirements',
                            'Fees paid for training are subject to the cancellation and refund policy outlined in Section 11',
                            'Completion of a course does not guarantee employment',
                        ]],
                        ['type' => 'h3', 'text' => '5.5 Career Programmes'],
                        ['type' => 'p', 'text' => 'Internship and apprenticeship programmes facilitated through the Platform are subject to the terms of the host organisation or employer. JCL will communicate relevant programme conditions at the point of application.'],
                    ],
                ],
                [
                    'id' => 'employers-and-organisations',
                    'number' => '6',
                    'title' => 'Employers and Organisations',
                    'blocks' => [
                        ['type' => 'h3', 'text' => '6.1 Job Postings'],
                        ['type' => 'p', 'text' => 'Employers may post job vacancies on the Platform subject to the following conditions:'],
                        ['type' => 'ul', 'items' => [
                            'All job listings must be genuine, lawful, and accurately described',
                            'Listings must not discriminate on the basis of gender, ethnicity, religion, disability, or any other protected characteristic',
                            'Employers must not use the Platform to collect candidate data for purposes other than legitimate recruitment',
                            'JCL reserves the right to reject, edit, or remove any listing that does not meet Platform standards',
                        ]],
                        ['type' => 'h3', 'text' => '6.2 Employability Standard'],
                        ['type' => 'p', 'text' => 'All candidates placed through JoseOceanJobs will have completed or be enrolled in a JoseOceanJobs-approved employability and Ethics training programme prior to deployment. Employers acknowledge and accept this standard as part of the JoseOceanJobs quality assurance process. This ensures every candidate presented is workplace-ready and aligned with the professional standards expected in the maritime and energy sectors.'],
                        ['type' => 'h3', 'text' => '6.3 Candidate Data'],
                        ['type' => 'p', 'text' => 'Candidate CVs, profiles, and application data shared with employers through the Platform must be used solely for the purpose of evaluating candidates for the specific role advertised. Employers must not share, sell, or repurpose candidate data without the candidate’s explicit consent.'],
                        ['type' => 'h3', 'text' => '6.4 Service Timeline'],
                        ['type' => 'p', 'text' => 'Suitable candidates will typically be presented within one to three weeks after confirmation of the Employer’s requirements.'],
                        ['type' => 'h3', 'text' => '6.5 Workforce and Staffing Solutions'],
                        ['type' => 'p', 'text' => 'Where an employer engages JCL for contract staffing, crew management, or cadet placement services, the specific terms, fees, timelines, and obligations will be set out in a separate Service Agreement. These Terms apply in conjunction with, not instead of, that agreement.'],
                        ['type' => 'h3', 'text' => '6.6 Fees'],
                        ['type' => 'p', 'text' => 'Recruitment and staffing service fees are agreed prior to engagement and communicated in writing. Payment terms are outlined in Section 10 of these Terms.'],
                    ],
                ],
                [
                    'id' => 'marine-and-business-service-clients',
                    'number' => '7',
                    'title' => 'Marine and Business Service Clients',
                    'blocks' => [
                        ['type' => 'h3', 'text' => '7.1 Service Requests'],
                        ['type' => 'p', 'text' => 'Clients requesting marine or business services through the Platform (including crew management, ship chandelling, marine procurement, marine insurance, travel management, mobilisation services, or global opportunity services) agree that:'],
                        ['type' => 'ul', 'items' => [
                            'All service requests must be submitted accurately and in good faith',
                            'JCL will coordinate delivery through its partner network; the identity of delivery partners will not be disclosed',
                            'JCL will remain the sole point of contact throughout the service engagement',
                            'Response timelines will be communicated at the point of request',
                        ]],
                        ['type' => 'h3', 'text' => '7.2 Service Agreements'],
                        ['type' => 'p', 'text' => 'Specific service engagements will be governed by a Service Level Agreement or formal brief confirming scope, deliverables, fees, and timelines. These Terms form the baseline governing framework for all such engagements.'],
                        ['type' => 'h3', 'text' => '7.3 Client Obligations'],
                        ['type' => 'p', 'text' => 'Clients are responsible for providing complete and accurate briefs to enable effective service delivery. JCL accepts no liability for delays or failures caused by incomplete, inaccurate, or late instructions from the client.'],
                    ],
                ],
                [
                    'id' => 'training-services',
                    'number' => '8',
                    'title' => 'Training Services',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'JCL offers training programmes in the following areas through the Platform:'],
                        ['type' => 'ul', 'items' => [
                            'Soft skills and professional development',
                            'Technical and non-technical maritime and energy skills',
                            'Industry-recognised certifications (delivered in partnership with accredited training providers)',
                        ]],
                        ['type' => 'p', 'text' => 'By enrolling in any training programme, you agree to:'],
                        ['type' => 'ul', 'items' => [
                            'Attend or complete the programme as scheduled',
                            'Comply with the rules and requirements of the training provider',
                            'Not reproduce, distribute, or commercially exploit any training materials provided',
                        ]],
                        ['type' => 'p', 'text' => 'Where a training programme is delivered by a partner institution, the partner’s own terms of enrolment may also apply. These will be communicated at the time of enrolment.'],
                    ],
                ],
                [
                    'id' => 'acceptable-use',
                    'number' => '9',
                    'title' => 'Acceptable Use',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'By using the Platform, you agree not to:'],
                        ['type' => 'ul', 'items' => [
                            'Submit false, misleading, or fraudulent information of any kind',
                            'Impersonate any person, organisation, or entity',
                            'Use the Platform to harass, threaten, or harm any other user',
                            'Attempt to gain unauthorised access to any part of the Platform or its systems',
                            'Use automated tools, bots, or scrapers to extract data from the Platform',
                            'Post or transmit any content that is unlawful, offensive, defamatory, or in violation of any third-party rights',
                            'Use candidate or employer data for any purpose other than the intended use on the Platform',
                            'Interfere with or disrupt the operation of the Platform',
                        ]],
                        ['type' => 'p', 'text' => 'JCL reserves the right to suspend or permanently remove any user who violates these standards, without notice and without liability.'],
                    ],
                ],
                [
                    'id' => 'fees-and-payment',
                    'number' => '10',
                    'title' => 'Fees and Payment',
                    'blocks' => [
                        ['type' => 'h3', 'text' => '10.1 Service Fees'],
                        ['type' => 'p', 'text' => 'Fees for recruitment services, staffing solutions, training enrolments, and marine services will be communicated in writing prior to engagement. No service will be invoiced without prior agreement.'],
                        ['type' => 'h3', 'text' => '10.2 Recruitment & Staffing Payment Structure'],
                        ['type' => 'p', 'text' => 'For all recruitment, crew management, contract staffing, and candidate placement services, the following payment structure applies:'],
                        ['type' => 'ul', 'items' => [
                            '70% of the agreed fee is due and payable before JCL commences candidate selection, screening, or any service delivery activity. This upfront payment is non-refundable once JCL has commenced service delivery activities',
                            'The remaining 30% is due upon successful selection and confirmation of the candidate or completion of the agreed service deliverable',
                            'No candidate will be presented, selected, or deployed to an employer until the 70% upfront payment has been received and confirmed by JCL',
                            'The 30% balance must be settled within 7 days of candidate confirmation or service completion, whichever is earlier',
                        ]],
                        ['type' => 'h3', 'text' => '10.3 Payment Terms'],
                        ['type' => 'p', 'text' => 'Unless otherwise agreed in writing:'],
                        ['type' => 'ul', 'items' => [
                            'Invoices are due within the payment period stated on the invoice',
                            'Payment reminders will be issued at 7, 14, and 30 days overdue',
                            'JCL reserves the right to suspend services for accounts with outstanding balances beyond 30 days',
                            'Disputed invoices must be raised in writing within 7 days of receipt',
                        ]],
                        ['type' => 'h3', 'text' => '10.4 Currency'],
                        ['type' => 'p', 'text' => 'All fees are quoted in Nigerian Naira (NGN) unless otherwise agreed. International clients may be invoiced in USD or GBP by prior agreement.'],
                    ],
                ],
                [
                    'id' => 'cancellations-and-refunds',
                    'number' => '11',
                    'title' => 'Cancellations and Refunds',
                    'blocks' => [
                        ['type' => 'h3', 'text' => '11.1 Training Enrolments'],
                        ['type' => 'p', 'text' => 'Cancellation and refund terms for training programmes are as follows:'],
                        ['type' => 'ul', 'items' => [
                            'Cancellation more than 14 days before programme start: full refund',
                            'Cancellation between 7 and 14 days before programme start: 50% refund',
                            'Cancellation less than 7 days before programme start: no refund',
                            'No-shows without prior notice: no refund',
                        ]],
                        ['type' => 'h3', 'text' => '11.2 Service Engagements'],
                        ['type' => 'p', 'text' => 'Cancellation of an active service engagement must be submitted in writing to info@joseoceanjobs.com. Refund or cancellation terms for service engagements will be as stated in the applicable Service Agreement.'],
                        ['type' => 'h3', 'text' => '11.3 Platform Subscription or Listing Fees'],
                        ['type' => 'p', 'text' => 'Where applicable, listing or subscription fees are non-refundable once the service period has commenced.'],
                    ],
                ],
                [
                    'id' => 'intellectual-property',
                    'number' => '12',
                    'title' => 'Intellectual Property',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'All content on the Platform — including but not limited to the JoseOceanJobs and JCL brand, logos, text, course materials, graphics, and platform design — is the property of Jose Consulting Limited or its licensed partners. You may not copy, reproduce, distribute, or use any Platform content for commercial purposes without the prior written consent of JCL.'],
                        ['type' => 'p', 'text' => 'By uploading content to the Platform (such as a CV, profile photo, or job listing), you grant JCL a non-exclusive, royalty-free licence to use that content for the purposes of operating and improving the Platform.'],
                    ],
                ],
                [
                    'id' => 'privacy-and-data-protection',
                    'number' => '13',
                    'title' => 'Privacy and Data Protection',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'JCL is committed to protecting your personal data in accordance with the Nigeria Data Protection Act 2023 (NDPA) and other applicable data protection laws.'],
                        ['type' => 'p', 'text' => 'By using the Platform, you consent to the collection and use of your personal data as described in our Privacy Policy, which is incorporated into these Terms by reference.'],
                        ['type' => 'p', 'text' => 'Key principles:'],
                        ['type' => 'ul', 'items' => [
                            'Your data is collected only for legitimate, stated purposes',
                            'Your data will not be sold to third parties',
                            'Candidate data shared with employers is used solely for recruitment evaluation',
                            'You have the right to access, correct, or request deletion of your personal data at any time by contacting info@joseoceanjobs.com',
                        ]],
                    ],
                ],
                [
                    'id' => 'limitation-of-liability',
                    'number' => '14',
                    'title' => 'Limitation of Liability',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'To the fullest extent permitted by law, Jose Consulting Limited shall not be liable for:'],
                        ['type' => 'ul', 'items' => [
                            'Any failure to secure employment, placement, or a specific outcome from using the Platform',
                            'Losses arising from inaccurate information provided by employers, candidates, or service clients',
                            'Delays or failures in service delivery caused by partner providers, third parties, or circumstances beyond JCL’s reasonable control',
                            'Loss of data, interruption of service, or technical failures of the Platform',
                            'Any indirect, consequential, or special loss arising from the use of or inability to use the Platform',
                        ]],
                        ['type' => 'p', 'text' => 'Nothing in these Terms limits JCL’s liability for fraud, gross negligence, or any matter that cannot be excluded by law.'],
                        ['type' => 'p', 'text' => 'To the fullest extent permitted by law, JCL’s total liability arising out of or relating to these Terms shall not exceed the total amount paid by the user to JCL during the twelve (12) months preceding the event giving rise to the claim.'],
                    ],
                ],
                [
                    'id' => 'disclaimers',
                    'number' => '15',
                    'title' => 'Disclaimers',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'The Platform is provided on an “as is” and “as available” basis. JCL makes no warranty, express or implied, that:'],
                        ['type' => 'ul', 'items' => [
                            'The Platform will be uninterrupted, error-free, or free from viruses',
                            'Any job listing, service, or training programme will meet your specific requirements',
                            'Information on the Platform is always current, accurate, or complete',
                        ]],
                        ['type' => 'p', 'text' => 'JCL does not endorse any employer, candidate, training provider, or service partner listed or featured on the Platform.'],
                    ],
                ],
                [
                    'id' => 'termination',
                    'number' => '16',
                    'title' => 'Termination',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'JCL may suspend or terminate your access to the Platform at any time, with or without notice, if:'],
                        ['type' => 'ul', 'items' => [
                            'You breach any provision of these Terms',
                            'You provide false or misleading information',
                            'Your use of the Platform causes harm to JCL, other users, or third parties',
                            'JCL is required to do so by law or regulatory authority',
                        ]],
                        ['type' => 'p', 'text' => 'You may terminate your account at any time by contacting info@joseoceanjobs.com. Termination does not affect any outstanding payment obligations or active service agreements.'],
                    ],
                ],
                [
                    'id' => 'changes-to-these-terms',
                    'number' => '17',
                    'title' => 'Changes to These Terms',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'JCL reserves the right to update or modify these Terms at any time. Where changes are material, we will notify registered users by email or by prominent notice on the Platform. Your continued use of the Platform after the effective date of any changes constitutes acceptance of the updated Terms.'],
                        ['type' => 'p', 'text' => 'The current version of these Terms is always available at joseoceanjobs.com.'],
                    ],
                ],
                [
                    'id' => 'force-majeure',
                    'number' => '18',
                    'title' => 'Force Majeure',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'Neither party shall be liable for any failure or delay in performing its obligations where such failure or delay results from events beyond its reasonable control, including but not limited to acts of God, war, terrorism, civil unrest, labour disputes, epidemics, pandemics, governmental actions, power failures, port closures, regulatory restrictions, or disruptions to internet or telecommunications services. The affected party shall notify the other as soon as reasonably practicable and shall use reasonable endeavours to resume performance.'],
                    ],
                ],
                [
                    'id' => 'indemnity',
                    'number' => '19',
                    'title' => 'Indemnity',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'Users agree to indemnify and hold harmless Jose Consulting Limited, its directors, employees, agents, and affiliates from any claims, losses, liabilities, damages, costs, or expenses (including reasonable legal fees) arising from: (a) breach of these Terms; (b) misrepresentation or false information provided by the user; (c) violation of any applicable law or third-party rights; or (d) misuse of the Platform or services.'],
                    ],
                ],
                [
                    'id' => 'anti-circumvention',
                    'number' => '20',
                    'title' => 'Anti-Circumvention',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'Employers introduced to candidates through the Platform shall not directly or indirectly engage, hire, contract, or otherwise employ such candidates outside the agreed recruitment arrangement for a period of twelve (12) months from the date of introduction without paying the applicable placement fee to JCL. Breach of this clause entitles JCL to claim the full agreed placement fee as a debt immediately due and payable.'],
                    ],
                ],
                [
                    'id' => 'candidate-verification-disclaimer',
                    'number' => '21',
                    'title' => 'Candidate Verification Disclaimer',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'While JCL may conduct verification checks on candidate credentials, JCL does not warrant the accuracy, completeness, or authenticity of information provided by candidates or employers. Users remain responsible for conducting their own independent due diligence prior to making any hiring, engagement, or business decision.'],
                    ],
                ],
                [
                    'id' => 'confidentiality',
                    'number' => '22',
                    'title' => 'Confidentiality',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'Users shall keep confidential all non-public information received through the Platform, including but not limited to candidate profiles, employer briefs, pricing, partner details, and service methodologies. Users shall not disclose such information to any third party except as required by law or with the prior written consent of JCL.'],
                    ],
                ],
                [
                    'id' => 'independent-contractor-relationship',
                    'number' => '23',
                    'title' => 'Independent Contractor Relationship',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'Nothing in these Terms shall be construed as creating an employment relationship, partnership, joint venture, agency, or fiduciary relationship between JCL and any user, candidate, employer, or partner organisation.'],
                    ],
                ],
                [
                    'id' => 'electronic-communications',
                    'number' => '24',
                    'title' => 'Electronic Communications',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'Users consent to receive notices, invoices, communications, agreements, and other documents electronically via email or the Platform. Electronic communications shall satisfy any legal requirement that such communications be in writing.'],
                    ],
                ],
                [
                    'id' => 'complaints-procedure',
                    'number' => '25',
                    'title' => 'Complaints Procedure',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'Complaints regarding services, recruitment processes, training programmes, or Platform use should be submitted in writing to info@joseoceanjobs.com. JCL will endeavour to acknowledge complaints within five (5) business days and investigate them promptly and fairly.'],
                    ],
                ],
                [
                    'id' => 'severability',
                    'number' => '26',
                    'title' => 'Severability',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'If any provision of these Terms is found to be invalid, illegal, or unenforceable by a court of competent jurisdiction, the remaining provisions shall remain in full force and effect.'],
                    ],
                ],
                [
                    'id' => 'waiver',
                    'number' => '27',
                    'title' => 'Waiver',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'Failure by JCL to enforce any provision of these Terms on any occasion shall not constitute a waiver of that provision or of any other right under these Terms.'],
                    ],
                ],
                [
                    'id' => 'assignment',
                    'number' => '28',
                    'title' => 'Assignment',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'Users may not assign or transfer their rights or obligations under these Terms without the prior written consent of JCL. JCL may assign its rights and obligations to any affiliate, successor, or purchaser of its business without restriction.'],
                    ],
                ],
                [
                    'id' => 'entire-agreement',
                    'number' => '29',
                    'title' => 'Entire Agreement',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'These Terms, together with the Privacy Policy and any applicable Service Agreement, constitute the entire agreement between the parties with respect to the subject matter herein and supersede all prior discussions, representations, and agreements.'],
                    ],
                ],
                [
                    'id' => 'governing-law-and-disputes',
                    'number' => '30',
                    'title' => 'Governing Law and Disputes',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'These Terms are governed by and construed in accordance with the laws of the Federal Republic of Nigeria.'],
                        ['type' => 'p', 'text' => 'Any dispute arising from or in connection with these Terms shall first be referred to mediation. If mediation fails, disputes shall be resolved by the courts of competent jurisdiction in Lagos, Nigeria.'],
                    ],
                ],
                [
                    'id' => 'contact-us',
                    'number' => '31',
                    'title' => 'Contact Us',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'If you have any questions about these Terms, wish to raise a complaint, or need to exercise any of your rights under these Terms, please contact us:'],
                        ['type' => 'contact', 'contact' => [
                            'org' => 'Jose Consulting Limited',
                            'trading' => 'JoseOceanJobs',
                            'website' => 'joseoceanjobs.com',
                            'email' => 'info@joseoceanjobs.com',
                            'whatsapp' => '+234 902 430 4210',
                            'location' => 'Lagos, Nigeria',
                        ]],
                    ],
                ],
            ],
        ];
    }

    /**
     * Definition of Terms — lead-in + 11 defined terms.
     * Each definition is stored WITHOUT the leading "means", which the view prepends.
     */
    public static function definitions(): array
    {
        return [
            'intro' => 'In these Terms, the following words and expressions shall have the meanings set out below, unless the context otherwise requires:',
            'terms' => [
                ['term' => 'Platform', 'definition' => 'the JoseOceanJobs website accessible at joseoceanjobs.com, including all associated pages, portals, features, tools, and digital services made available through it.'],
                ['term' => 'JCL', 'definition' => 'Jose Consulting Limited, a company registered in Nigeria, trading as JoseOceanJobs, which owns and operates the Platform.'],
                ['term' => 'User', 'definition' => 'any individual or organisation that accesses, registers on, or otherwise uses the Platform in any capacity, whether as a Candidate, Employer, Training Participant, Client, or Visitor.'],
                ['term' => 'Candidate', 'definition' => 'an individual who registers on the Platform as a job seeker, including any person who creates a profile, uploads a CV, applies for a vacancy, or seeks access to career pathways, training, or certification programmes through the Platform.'],
                ['term' => 'Employer', 'definition' => 'any company, vessel operator, oil and gas firm, organisation, or other entity that registers on the Platform to post vacancies, source talent, or engage JCL for workforce solutions, including recruitment, crew management, or contract staffing services.'],
                ['term' => 'Training Participant', 'definition' => 'an individual who enrols in or participates in any training programme, certification course, professional development programme, internship, or apprenticeship offered or facilitated through the Platform.'],
                ['term' => 'Client', 'definition' => 'any organisation or individual that engages JCL through the Platform to receive marine or business services, including crew management, ship chandelling, marine procurement, marine insurance, travel management, mobilisation services, or global opportunity services.'],
                ['term' => 'Services', 'definition' => 'all services offered, facilitated, or coordinated by JCL through the Platform, including but not limited to job listing and matching, recruitment and staffing solutions, training and professional development programmes, marine and business services, career pathway programmes, and workforce consulting.'],
                ['term' => 'Terms', 'definition' => 'these Terms of Service, as amended from time to time in accordance with Section 18.'],
                ['term' => 'Service Agreement', 'definition' => 'any separate written agreement, service level agreement, or formal brief entered into between JCL and a User, Employer, or Client in connection with a specific engagement or service, which shall be read together with these Terms.'],
                ['term' => 'Visitor', 'definition' => 'any individual who accesses or browses the Platform without registering an account. These Terms apply to Visitors in respect of their use of the Platform, to the extent applicable.'],
            ],
        ];
    }

    /**
     * Cookie Policy — meta header + ordered sections.
     *
     * Content is grounded in an audit of what the Platform actually sets:
     * three first-party cookies (session, CSRF, optional "remember me") and a
     * small set of third-party services (Paystack, Google/Microsoft sign-in,
     * Google Fonts). The Platform runs no analytics, advertising or tracking
     * cookies — keep this accurate if that ever changes.
     */
    public static function cookiePolicy(): array
    {
        return [
            'meta' => [
                'effective' => 'Effective Date: 2026',
                'version' => 'Version 1.0',
                'site' => 'joseoceanjobs.com',
            ],
            'sections' => [
                [
                    'id' => 'introduction',
                    'number' => '1',
                    'title' => 'Introduction',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'This Cookie Policy explains how Jose Consulting Limited (JCL), which owns and operates JoseOceanJobs at joseoceanjobs.com (the "Platform"), uses cookies and similar technologies when you visit or use the Platform.'],
                        ['type' => 'p', 'text' => 'It should be read together with our Privacy Policy, which explains how we handle your personal data more generally. By using the Platform, you agree to the use of cookies as described in this policy, except where your consent is separately required.'],
                    ],
                ],
                [
                    'id' => 'what-are-cookies',
                    'number' => '2',
                    'title' => 'What Are Cookies',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'Cookies are small text files that a website places on your device (computer, tablet, or phone) when you visit. They are widely used to make websites work, to keep you signed in, and to remember your preferences.'],
                        ['type' => 'p', 'text' => 'Cookies set by the Platform itself are called "first-party" cookies. Cookies set by another organisation — for example a payment provider whose page you are redirected to — are called "third-party" cookies. Cookies may last only for your current visit ("session" cookies) or remain on your device for a set period ("persistent" cookies).'],
                    ],
                ],
                [
                    'id' => 'how-we-use-cookies',
                    'number' => '3',
                    'title' => 'How We Use Cookies',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'We keep our use of cookies to the minimum needed to run the Platform securely. We use cookies in the following categories only:'],
                        ['type' => 'ul', 'items' => [
                            'Strictly necessary cookies — required for the Platform to function, such as keeping you signed in and protecting forms against fraud. The Platform cannot work properly without these, so they cannot be switched off through our site.',
                            'Functional cookies — remember a choice you have made, such as staying signed in when you select "Remember me" at login.',
                        ]],
                        ['type' => 'p', 'text' => 'Importantly, the Platform does NOT use analytics cookies, advertising or marketing cookies, social-media tracking pixels, or any cross-site behavioural tracking. We do not build advertising profiles, and we do not sell your data.'],
                    ],
                ],
                [
                    'id' => 'cookies-we-use',
                    'number' => '4',
                    'title' => 'Cookies We Use',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'The Platform sets the following first-party cookies:'],
                        ['type' => 'table', 'table' => [
                            'head' => ['Cookie', 'Purpose', 'Category', 'Duration'],
                            'rows' => [
                                ['joseoceanjobs-session', 'Identifies your browsing session so you stay signed in and your activity is preserved as you move between pages. It contains only an encrypted reference; your information is stored securely on our server, not in the cookie.', 'Strictly necessary', 'Expires about 2 hours after inactivity (not stored permanently).'],
                                ['XSRF-TOKEN', 'Protects forms and requests against cross-site request forgery (CSRF), a common security attack.', 'Strictly necessary', 'Session.'],
                                ['remember_web_*', 'Keeps you signed in between visits, but only if you tick "Remember me" when logging in.', 'Functional', 'Persistent — until you log out (up to several years if not cleared).'],
                            ],
                        ]],
                        ['type' => 'p', 'text' => 'Cookie names may vary slightly depending on configuration, but their purpose remains as described above.'],
                    ],
                ],
                [
                    'id' => 'third-party-services',
                    'number' => '5',
                    'title' => 'Third-Party Services',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'In certain situations the Platform relies on trusted third-party services. These services may set their own cookies or receive limited technical information (such as your IP address) directly, governed by their own privacy and cookie policies — not this one. They are only triggered in the circumstances described below:'],
                        ['type' => 'ul', 'items' => [
                            'Paystack (payments) — if you choose to pay online, you are securely redirected to Paystack to complete the transaction. Paystack sets its own cookies on its checkout pages for security and fraud prevention. See paystack.com for details. We do not store your full card details.',
                            'Google and Microsoft sign-in — if you choose to sign in using your Google or Microsoft account, you are redirected to that provider, which sets its own cookies to authenticate you. See the privacy policies of Google and Microsoft.',
                            'Google Fonts — the Platform loads fonts from Google\'s servers so pages display consistently. This does not set a cookie, but it does send your IP address and browser information to Google when fonts are requested.',
                        ]],
                        ['type' => 'p', 'text' => 'We also use a real-time messaging service to deliver live notifications and chat. This relies on your existing session cookie above to confirm your identity; it does not place any additional cookie on your device.'],
                    ],
                ],
                [
                    'id' => 'consent',
                    'number' => '6',
                    'title' => 'Do We Ask for Your Consent',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'Because the Platform currently uses only strictly necessary and functional cookies — and no analytics, advertising, or tracking cookies — we rely on these cookies being essential to providing a service you have requested, and we do not display a cookie consent banner.'],
                        ['type' => 'p', 'text' => 'If in future we introduce analytics, advertising, or other non-essential cookies, we will update this policy and ask for your consent before those cookies are placed, in line with applicable data protection law.'],
                    ],
                ],
                [
                    'id' => 'managing-cookies',
                    'number' => '7',
                    'title' => 'Managing and Disabling Cookies',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'You can control and delete cookies through your browser settings. Most browsers let you view what cookies are stored, block all or some cookies, and delete cookies already set. Instructions are available in the help section of your browser (for example Chrome, Safari, Firefox, or Edge).'],
                        ['type' => 'p', 'text' => 'Please note that blocking strictly necessary cookies will prevent parts of the Platform from working correctly — in particular, you will not be able to sign in or stay signed in.'],
                    ],
                ],
                [
                    'id' => 'changes-to-this-policy',
                    'number' => '8',
                    'title' => 'Changes to This Policy',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'We may update this Cookie Policy from time to time to reflect changes in the technologies we use or in the law. Where changes are material, we will provide notice on the Platform. The current version is always available at joseoceanjobs.com.'],
                    ],
                ],
                [
                    'id' => 'contact-us',
                    'number' => '9',
                    'title' => 'Contact Us',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'If you have any questions about how we use cookies, please contact us:'],
                        ['type' => 'contact', 'contact' => [
                            'org' => 'Jose Consulting Limited',
                            'trading' => 'JoseOceanJobs',
                            'website' => 'joseoceanjobs.com',
                            'email' => 'info@joseoceanjobs.com',
                            'whatsapp' => '+234 902 430 4210',
                            'location' => 'Lagos, Nigeria',
                        ]],
                    ],
                ],
            ],
        ];
    }

    /**
     * Privacy Policy — meta header + ordered sections.
     *
     * Grounded in what the Platform actually collects (accounts, candidate and
     * company profiles, CV uploads, applications, payments via Paystack, optional
     * Google/Microsoft sign-in, chat messages) and aligned with the Nigeria Data
     * Protection Act 2023 referenced in the Terms of Service.
     */
    public static function privacyPolicy(): array
    {
        return [
            'meta' => [
                'effective' => 'Effective Date: 2026',
                'version' => 'Version 1.0',
                'site' => 'joseoceanjobs.com',
            ],
            'sections' => [
                [
                    'id' => 'introduction',
                    'number' => '1',
                    'title' => 'Introduction',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'Jose Consulting Limited (JCL), a company registered in Nigeria and trading as JoseOceanJobs, is committed to protecting your privacy. This Privacy Policy explains what personal data we collect through joseoceanjobs.com (the "Platform"), how we use it, who we share it with, and the rights you have.'],
                        ['type' => 'p', 'text' => 'We process personal data in accordance with the Nigeria Data Protection Act 2023 (NDPA) and other applicable data protection laws. For information specifically about cookies, please see our Cookie Policy.'],
                    ],
                ],
                [
                    'id' => 'information-we-collect',
                    'number' => '2',
                    'title' => 'Information We Collect',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'We collect the following categories of personal data, depending on how you use the Platform:'],
                        ['type' => 'ul', 'items' => [
                            'Account information — your name, email address, phone number, password (stored encrypted), and the role you register under (candidate or employer).',
                            'Candidate profile data — where you register as a job seeker: job title, biography, date of birth, gender, education and work experience, skills, location, expected salary, social or professional links, profile photo, and any CV or resume documents you upload.',
                            'Employer and company data — where you register as an employer: company name, contact details, logo, location, and related company information.',
                            'Application and recruitment data — the jobs you apply for, application status, saved jobs, job alerts, and information exchanged during the recruitment process.',
                            'Payment information — when you make a payment, transactions are processed by our payment provider (Paystack). We receive confirmation and reference details, but we do not collect or store your full card details.',
                            'Communications — messages you send through the in-platform chat, enquiries you submit through contact or service-request forms, and related correspondence.',
                            'Sign-in provider data — if you choose to sign in with Google or Microsoft, we receive basic profile information (such as your name and email) from that provider.',
                            'Technical data — limited information such as your IP address, browser type, and activity on the Platform, including information collected through cookies as described in our Cookie Policy.',
                        ]],
                    ],
                ],
                [
                    'id' => 'how-we-use-your-information',
                    'number' => '3',
                    'title' => 'How We Use Your Information',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'We use your personal data to:'],
                        ['type' => 'ul', 'items' => [
                            'Create and manage your account and provide the Platform\'s features',
                            'Match candidates with employers and process job applications',
                            'Deliver training, recruitment, and marine or business services you request',
                            'Process payments and send receipts and related confirmations',
                            'Enable messaging and send service notifications relevant to your account',
                            'Verify information and maintain the security and integrity of the Platform',
                            'Respond to your enquiries, requests, and complaints',
                            'Comply with our legal and regulatory obligations',
                        ]],
                    ],
                ],
                [
                    'id' => 'legal-basis',
                    'number' => '4',
                    'title' => 'Legal Basis for Processing',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'Under the NDPA, we rely on one or more of the following legal bases when processing your personal data:'],
                        ['type' => 'ul', 'items' => [
                            'Performance of a contract — to provide the services you have requested and operate your account',
                            'Consent — where you have given clear consent, for example to share your profile with an employer (you may withdraw consent at any time)',
                            'Legitimate interests — to keep the Platform secure, prevent fraud, and improve our services, provided your rights are not overridden',
                            'Legal obligation — where we must process data to comply with the law',
                        ]],
                    ],
                ],
                [
                    'id' => 'sharing-your-information',
                    'number' => '5',
                    'title' => 'Sharing Your Information',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'We do not sell your personal data. We share it only where necessary, including with:'],
                        ['type' => 'ul', 'items' => [
                            'Employers — candidate profiles and application data are shared with the relevant employer solely for evaluating you for a specific role',
                            'Delivery partners — where you request marine, training, or business services, we coordinate delivery through our vetted partner network',
                            'Payment provider — Paystack, to process online payments securely',
                            'Service providers — trusted providers who help us operate the Platform (for example hosting and email delivery), under appropriate confidentiality obligations',
                            'Authorities — where required by law, regulation, or valid legal process',
                        ]],
                        ['type' => 'p', 'text' => 'Employers who receive candidate data must use it only for the advertised role and must not share, sell, or repurpose it without the candidate\'s explicit consent.'],
                    ],
                ],
                [
                    'id' => 'data-retention',
                    'number' => '6',
                    'title' => 'Data Retention',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'We keep your personal data for as long as your account is active and for as long as needed to provide our services, comply with legal obligations, resolve disputes, and enforce our agreements. When data is no longer needed, we securely delete or anonymise it. You may request deletion of your account and data as described below.'],
                    ],
                ],
                [
                    'id' => 'your-rights',
                    'number' => '7',
                    'title' => 'Your Rights',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'Under the NDPA, you have the right to:'],
                        ['type' => 'ul', 'items' => [
                            'Access the personal data we hold about you',
                            'Request correction of inaccurate or incomplete data',
                            'Request deletion of your personal data',
                            'Object to or restrict certain processing',
                            'Request a copy of your data in a portable format',
                            'Withdraw consent at any time, where processing is based on consent',
                            'Lodge a complaint with the Nigeria Data Protection Commission',
                        ]],
                        ['type' => 'p', 'text' => 'To exercise any of these rights, contact us at info@joseoceanjobs.com. We will respond within the timeframes required by law.'],
                    ],
                ],
                [
                    'id' => 'data-security',
                    'number' => '8',
                    'title' => 'Data Security',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'We use appropriate technical and organisational measures to protect your personal data, including encrypted passwords, access controls, and secure handling of payment transactions through our payment provider. However, no method of transmission or storage is completely secure, and we cannot guarantee absolute security.'],
                    ],
                ],
                [
                    'id' => 'cookies',
                    'number' => '9',
                    'title' => 'Cookies',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'The Platform uses a small number of strictly necessary and functional cookies to operate securely and keep you signed in. We do not use analytics, advertising, or tracking cookies. Full details are set out in our Cookie Policy.'],
                    ],
                ],
                [
                    'id' => 'international-transfers',
                    'number' => '10',
                    'title' => 'International Transfers',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'Some of our service providers (such as payment, hosting, or sign-in providers) may process data outside Nigeria. Where this happens, we take reasonable steps to ensure your data remains protected in accordance with the NDPA.'],
                    ],
                ],
                [
                    'id' => 'childrens-privacy',
                    'number' => '11',
                    'title' => 'Children\'s Privacy',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'The Platform is intended for users who are at least 18 years of age. We do not knowingly collect personal data from anyone under 18. If we become aware that we have done so, we will delete that data.'],
                    ],
                ],
                [
                    'id' => 'changes-to-this-policy',
                    'number' => '12',
                    'title' => 'Changes to This Policy',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'We may update this Privacy Policy from time to time. Where changes are material, we will notify registered users by email or by prominent notice on the Platform. The current version is always available at joseoceanjobs.com.'],
                    ],
                ],
                [
                    'id' => 'contact-us',
                    'number' => '13',
                    'title' => 'Contact Us',
                    'blocks' => [
                        ['type' => 'p', 'text' => 'If you have any questions about this Privacy Policy or wish to exercise your rights, please contact us:'],
                        ['type' => 'contact', 'contact' => [
                            'org' => 'Jose Consulting Limited',
                            'trading' => 'JoseOceanJobs',
                            'website' => 'joseoceanjobs.com',
                            'email' => 'info@joseoceanjobs.com',
                            'whatsapp' => '+234 902 430 4210',
                            'location' => 'Lagos, Nigeria',
                        ]],
                    ],
                ],
            ],
        ];
    }
}
