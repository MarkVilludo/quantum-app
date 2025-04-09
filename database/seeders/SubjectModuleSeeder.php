<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\SubjectModule;

class SubjectModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            1 => ['Introduction to Computing', 'History of Computers'],
            2 => ['Introduction to Computing', 'Operating Systems Overview'],
            3 => ['Computer Programming 1', 'Syntax and Semantics'],
            4 => ['Computer Programming 1', 'Control Structures'],
            5 => ['Discrete Mathematics', 'Logic and Proofs'],
            6 => ['Discrete Mathematics', 'Set Theory'],
            7 => ['Discrete Mathematics', 'Graph Theory'],
            8 => ['Digital Logic Design', 'Boolean Algebra'],
            9 => ['Digital Logic Design', 'Combinational Circuits'],
            10 => ['Digital Logic Design', 'Sequential Circuits'],
            11 => ['Human-Computer Interaction', 'Usability Principles'],
            12 => ['Human-Computer Interaction', 'User-Centered Design'],
            13 => ['Human-Computer Interaction', 'Cognitive Models'],
            14 => ['Data Structures and Algorithms', 'Linked Lists'],
            15 => ['Data Structures and Algorithms', 'Sorting Algorithms'],
            16 => ['Data Structures and Algorithms', 'Graph Algorithms'],
            17 => ['Computer Programming 2', 'OOP Principles'],
            18 => ['Computer Programming 2', 'Exception Handling'],
            19 => ['Computer Programming 2', 'Multithreading'],
            20 => ['Operating Systems', 'Memory Management'],
            21 => ['Operating Systems', 'Process Scheduling'],
            22 => ['Operating Systems', 'File Systems'],
            23 => ['Database Management Systems', 'SQL Basics'],
            24 => ['Database Management Systems', 'Normalization'],
            25 => ['Database Management Systems', 'Indexing'],
            26 => ['Object-Oriented Programming', 'Classes and Objects'],
            27 => ['Object-Oriented Programming', 'Inheritance'],
            28 => ['Software Engineering', 'SDLC Models'],
            29 => ['Software Engineering', 'Agile Methodology'],
            30 => ['Software Engineering', 'Software Testing'],
            31 => ['Computer Networks', 'Network Topologies'],
            32 => ['Computer Networks', 'TCP/IP Model'],
            33 => ['Computer Networks', 'Network Security'],
            34 => ['Web Development', 'HTML & CSS'],
            35 => ['Web Development', 'JavaScript'],
            36 => ['Web Development', 'Backend Frameworks'],
            37 => ['Mobile Application Development', 'Android Development'],
            38 => ['Mobile Application Development', 'iOS Development'],
            39 => ['Mobile Application Development', 'Cross-Platform Apps'],
            40 => ['IT Project Management', 'Project Planning'],
            41 => ['IT Project Management', 'Risk Management'],
            42 => ['IT Project Management', 'Scrum Framework'],
            43 => ['Artificial Intelligence', 'Machine Learning Basics'],
            44 => ['Artificial Intelligence', 'Neural Networks'],
            45 => ['Artificial Intelligence', 'AI Ethics'],
            46 => ['Cybersecurity', 'Encryption Techniques'],
            47 => ['Cybersecurity', 'Network Security'],
            48 => ['Cybersecurity', 'Penetration Testing'],
            49 => ['Cloud Computing', 'AWS & Azure Basics'],
            50 => ['Cloud Computing', 'Virtualization'],
            51 => ['Cloud Computing', 'Microservices'],
            52 => ['Capstone Project', 'Project Proposal'],
            53 => ['Capstone Project', 'Development Phases'],
            54 => ['Capstone Project', 'Final Presentation'],
            55 => ['Ethics in IT', 'Legal Aspects'],
            56 => ['Ethics in IT', 'Professional Conduct'],
            57 => ['Ethics in IT', 'Data Privacy Laws'],
        ];

        foreach ($modules as $moduleId => [$subjectName, $moduleName]) {
            $subject = Subject::where('name', $subjectName)->first();
            
            if ($subject) {
                SubjectModule::create([
                    'id' => $moduleId,
                    'name' => $moduleName,
                    'subject_id' => $subject->id,
                ]);
            }
        }
    }
}
