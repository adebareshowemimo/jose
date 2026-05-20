<?php

namespace App\Support;

class ContactRoutes
{
    public const DEFAULT_FALLBACK_EMAIL = 'info@joseoceanjobs.com';

    public const DEFAULT_ROUTES = [
        ['label' => 'General Inquiry',                              'email' => 'info@joseoceanjobs.com'],
        ['label' => 'Training — Soft Skills',                       'email' => 'support@joseoceanjobs.com'],
        ['label' => 'Training — Technical & Non Technical Skills',  'email' => 'support@joseoceanjobs.com'],
        ['label' => 'Crew Management',                              'email' => 'support@joseoceanjobs.com'],
        ['label' => 'Ship Chandelling',                             'email' => 'business@joseoceanjobs.com'],
        ['label' => 'Crew Abandonment Support',                     'email' => 'support@joseoceanjobs.com'],
        ['label' => 'Marine Procurement',                           'email' => 'business@joseoceanjobs.com'],
        ['label' => 'Marine Insurance',                             'email' => 'business@joseoceanjobs.com'],
        ['label' => 'Marine Travel',                                'email' => 'business@joseoceanjobs.com'],
        ['label' => 'Self-Employment Setup',                        'email' => 'support@joseoceanjobs.com'],
        ['label' => 'Job Placement Services',                       'email' => 'support@joseoceanjobs.com'],
        ['label' => 'Partnership Proposal',                         'email' => 'business@joseoceanjobs.com'],
    ];

    /**
     * @return array<int, array{label:string,email:string}>
     */
    public function subjects(): array
    {
        $stored = setting('contact.subject_routes');

        if (! is_array($stored) || empty($stored)) {
            return self::DEFAULT_ROUTES;
        }

        $subjects = array_values(array_filter(
            array_map(fn ($row) => [
                'label' => $this->displayLabel((string) ($row['label'] ?? '')),
                'email' => (string) ($row['email'] ?? ''),
            ], $stored),
            fn ($row) => $row['label'] !== '',
        ));

        return $this->ensureDefaultSubject($subjects, 'Self-Employment Setup', 'Marine Travel');
    }

    /**
     * @return array<int, string>
     */
    public function subjectLabels(): array
    {
        return array_column($this->subjects(), 'label');
    }

    public function defaultEmail(): string
    {
        $email = setting('contact.default_email');

        if (is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        return config('mail.from.address') ?: self::DEFAULT_FALLBACK_EMAIL;
    }

    public function routeFor(string $subject): string
    {
        $needle = $this->normalize($subject);

        foreach ($this->subjects() as $row) {
            if ($this->normalize($row['label']) === $needle
                && filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                return $row['email'];
            }
        }

        return $this->defaultEmail();
    }

    private function normalize(string $value): string
    {
        return mb_strtolower(trim($value));
    }

    private function displayLabel(string $label): string
    {
        return $this->normalize($label) === 'travel management service'
            ? 'Marine Travel'
            : $label;
    }

    /**
     * @param array<int, array{label:string,email:string}> $subjects
     * @return array<int, array{label:string,email:string}>
     */
    private function ensureDefaultSubject(array $subjects, string $label, ?string $afterLabel = null): array
    {
        $needle = $this->normalize($label);

        foreach ($subjects as $subject) {
            if ($this->normalize($subject['label']) === $needle) {
                return $subjects;
            }
        }

        foreach (self::DEFAULT_ROUTES as $defaultSubject) {
            if ($this->normalize($defaultSubject['label']) === $needle) {
                if ($afterLabel !== null) {
                    $afterNeedle = $this->normalize($afterLabel);

                    foreach ($subjects as $index => $subject) {
                        if ($this->normalize($subject['label']) === $afterNeedle) {
                            array_splice($subjects, $index + 1, 0, [$defaultSubject]);

                            return $subjects;
                        }
                    }
                }

                $subjects[] = $defaultSubject;
                break;
            }
        }

        return $subjects;
    }
}
