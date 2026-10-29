<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Year;
use App\Models\Subject;

class YearAndSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $years = [
            ['name' => 'First Year'],
            ['name' => 'Second Year'],
            ['name' => 'Third Year'],
            ['name' => 'Fourth Year']
        ];

        $subjects = [
            'First Year' => ['Introduction to Computing', 'Computer Programming 1', 'Discrete Mathematics', 'Digital Logic Design', 'Human-Computer Interaction'],
            'Second Year' => ['Data Structures and Algorithms', 'Computer Programming 2', 'Operating Systems', 'Database Management Systems', 'Object-Oriented Programming'],
            'Third Year' => ['Software Engineering', 'Computer Networks', 'Web Development', 'Mobile Application Development', 'IT Project Management'],
            'Fourth Year' => ['Artificial Intelligence', 'Cybersecurity', 'Cloud Computing', 'Capstone Project', 'Ethics in IT']
        ];

        foreach ($years as $yearData) {
            $year = Year::withTrashed()->firstOrCreate(
                ['name' => $yearData['name']]
            );

            if ($year->trashed()) {
                $year->restore();
            }

            Year::withTrashed()
                ->where('name', $year->name)
                ->where('id', '!=', $year->id)
                ->get()
                ->each(fn (Year $duplicate) => $duplicate->forceDelete());

            foreach ($subjects[$year->name] as $subjectName) {
                $subject = Subject::withTrashed()->firstOrCreate(
                    [
                        'name' => $subjectName,
                        'year_id' => $year->id,
                    ]
                );

                if ($subject->trashed()) {
                    $subject->restore();
                }
            }
        }
    }
}
