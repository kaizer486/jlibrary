<?php

namespace App\Constants;

class BookCategories
{
    // ==========================================
    // ALL CATEGORIES
    // ==========================================
    const ALL = [
        // Technology & Computing
        'computer_science' => 'Computer Science & Information Technology',
        'artificial_intelligence' => 'Artificial Intelligence & Data Science',
        'engineering' => 'Engineering & Technology',
        
        // Sciences
        'mathematics' => 'Mathematics & Statistics',
        'physical_sciences' => 'Physical Sciences',
        'biological_sciences' => 'Biological Sciences',
        'health_sciences' => 'Health & Medical Sciences',
        'public_health' => 'Public Health',
        'agriculture' => 'Agriculture & Veterinary Sciences',
        'environmental_sciences' => 'Environmental & Earth Sciences',
        
        // Business & Economics
        'business' => 'Business & Management',
        'economics' => 'Economics & Finance',
        'accounting' => 'Accounting',
        'marketing' => 'Marketing',
        'entrepreneurship' => 'Entrepreneurship',
        
        // Law & Education
        'law' => 'Law',
        'education' => 'Education',
        
        // Social Sciences
        'social_sciences' => 'Social Sciences',
        'psychology' => 'Psychology',
        'political_science' => 'Political Science & Public Administration',
        
        // Humanities
        'philosophy' => 'Philosophy',
        'languages' => 'Languages & Linguistics',
        'literature' => 'Literature',
        'history' => 'History & Archaeology',
        'geography' => 'Geography & Tourism',
        'religion' => 'Religion & Theology',
        
        // Arts & Design
        'arts' => 'Arts, Design & Music',
        'architecture' => 'Architecture & Urban Planning',
        
        // Literature & General
        'children' => "Children's Books",
        'fiction' => 'Fiction',
        'non_fiction' => 'Non-Fiction',
        'biographies' => 'Biographies & Memoirs',
        'self_help' => 'Self-Help & Personal Development',
        'leadership' => 'Leadership',
        
        // Academic & Research
        'research' => 'Research & Academic Publications',
        'journals' => 'Journals & Conference Proceedings',
        'theses' => 'Theses & Dissertations',
        'government' => 'Government Publications',
        'policies' => 'Policies, Acts & Regulations',
        'reports' => 'Reports & White Papers',
        
        // Reference & More
        'reference' => 'Reference Books',
        'oer' => 'Open Educational Resources (OER)',
        'newspapers' => 'Newspapers & Magazines',
        'encyclopedias' => 'Encyclopedias & Dictionaries',
    ];

    // ==========================================
    // GROUPED CATEGORIES FOR DROPDOWN
    // ==========================================
    const GROUPS = [
        '💻 Technology & Computing' => [
            'computer_science',
            'artificial_intelligence',
            'engineering',
        ],
        '🔬 Sciences' => [
            'mathematics',
            'physical_sciences',
            'biological_sciences',
            'health_sciences',
            'public_health',
            'agriculture',
            'environmental_sciences',
        ],
        '💼 Business & Economics' => [
            'business',
            'economics',
            'accounting',
            'marketing',
            'entrepreneurship',
        ],
        '⚖️ Law & Education' => [
            'law',
            'education',
        ],
        '🧠 Social Sciences' => [
            'social_sciences',
            'psychology',
            'political_science',
        ],
        '📜 Humanities' => [
            'philosophy',
            'languages',
            'literature',
            'history',
            'geography',
            'religion',
        ],
        '🎨 Arts & Design' => [
            'arts',
            'architecture',
        ],
        '📚 Literature & General' => [
            'children',
            'fiction',
            'non_fiction',
            'biographies',
            'self_help',
            'leadership',
        ],
        '📖 Academic & Research' => [
            'research',
            'journals',
            'theses',
            'government',
            'policies',
            'reports',
        ],
        '📖 Reference & More' => [
            'reference',
            'oer',
            'newspapers',
            'encyclopedias',
        ],
    ];

    /**
     * Get all categories.
     */
    public static function all(): array
    {
        return self::ALL;
    }

    /**
     * Get category label by key.
     */
    public static function label(string $key): string
    {
        return self::ALL[$key] ?? $key;
    }

    /**
     * Get grouped categories for dropdown.
     */
    public static function grouped(): array
    {
        $result = [];
        foreach (self::GROUPS as $group => $keys) {
            $result[$group] = [];
            foreach ($keys as $key) {
                $result[$group][$key] = self::ALL[$key] ?? $key;
            }
        }
        return $result;
    }
}