<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleLessonSeeder extends Seeder
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

        $lessons = [
            [
                'module_id' => 1, // Introduction to Computing -> History of Computers
                'name' => 'History of Computers',
                'data' => [
                    ['content' => 'The history of computing traces back to ancient civilizations that used devices like the abacus for arithmetic calculations. The earliest mechanical computing devices were designed to aid in basic mathematical operations. The concept of automated computing started evolving in the 17th century, with inventors like Blaise Pascal creating simple mechanical calculators to perform addition and subtraction.'],
                    ['content' => 'In the 19th century, Charles Babbage designed the Analytical Engine, considered the first mechanical computer. This device incorporated concepts such as input, processing, storage, and output, forming the foundation of modern computers. His assistant, Ada Lovelace, is credited as the first computer programmer, as she developed algorithms for the machine.'],
                    ['content' => 'The first-generation computers, developed in the 1940s and 1950s, relied on vacuum tubes for processing. These computers were massive, consuming a lot of power and generating excessive heat. Examples include the ENIAC, which was used for military calculations, and the UNIVAC, the first commercial computer.'],
                    ['content' => 'The second-generation computers, introduced in the late 1950s and 1960s, replaced vacuum tubes with transistors. This made computers smaller, more efficient, and more reliable. During this era, programming languages like COBOL and FORTRAN emerged, allowing for easier software development.'],
                    ['content' => 'The third-generation computers in the 1960s and 1970s saw the introduction of integrated circuits (ICs), which significantly improved processing power and efficiency. This period also saw the rise of operating systems, which allowed multiple users to interact with a computer simultaneously.'],
                    ['content' => 'The fourth-generation computers, starting from the 1970s, utilized microprocessors, which integrated thousands of transistors into a single chip. This led to the development of personal computers (PCs), making computing more accessible to businesses and individuals. Companies like Apple and IBM revolutionized the industry during this era.'],
                    ['content' => 'The internet boom in the 1990s further transformed computing, connecting devices across the world. Cloud computing emerged, allowing users to access and store data remotely. The advancements in networking technologies facilitated the rapid exchange of information and the growth of e-commerce and social media.'],
                    ['content' => 'Today, computing continues to evolve with artificial intelligence, machine learning, and quantum computing. AI-powered systems are being integrated into everyday applications, automating complex tasks. Quantum computing, although in its early stages, promises exponential improvements in processing capabilities.'],
                    ['content' => 'The future of computing involves emerging technologies such as neuromorphic computing, where processors mimic the human brain. Edge computing is also becoming popular, bringing processing power closer to users, reducing latency, and improving performance for real-time applications.'],
                    ['content' => 'Understanding the history of computing is essential for appreciating how technology has progressed over the years. From early mechanical calculators to advanced AI-driven systems, the field of computing has undergone remarkable transformations, shaping modern society.'],
                ],
                'activity' => [
                    ['title' => 'Quiz 1', 'question' => 'Who is considered the father of modern computers?', 'correct_answer' => 'Charles Babbage', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 2, // Introduction to Computing -> Operating Systems Overview
                'name' => 'Operating Systems Overview',
                'data' => [
                    ['content' => 'An operating system (OS) is a crucial software component that manages computer hardware and software resources. It acts as an intermediary between users and the computer hardware, facilitating interaction through a user-friendly interface. Without an OS, it would be nearly impossible for users to interact with a computer efficiently.'],
                    ['content' => 'The primary functions of an OS include process management, memory management, file system organization, and security enforcement. Process management ensures that multiple tasks can run concurrently, while memory management allocates resources efficiently to avoid conflicts.'],
                    ['content' => 'There are different types of operating systems, including batch processing, time-sharing, distributed, and real-time OS. Batch processing systems execute tasks without user interaction, whereas time-sharing OS allows multiple users to share system resources. Distributed systems connect multiple computers, and real-time OS are used in critical applications such as medical devices and industrial automation.'],
                    ['content' => 'Popular operating systems include Microsoft Windows, Linux, macOS, and Unix. Each of these OS offers unique features, catering to different user needs. Windows is widely used for personal and business computing, Linux is favored for servers and development environments, and macOS is preferred by creative professionals.'],
                    ['content' => 'One of the key components of an OS is the kernel, which serves as the core that directly interacts with hardware. The kernel is responsible for managing system calls, allocating memory, and handling input/output operations. There are different types of kernels, such as monolithic kernels and microkernels, each with its advantages and disadvantages.'],
                    ['content' => 'Virtual memory is an essential feature of modern operating systems, allowing the system to use disk space as temporary RAM when physical memory is insufficient. This technique enables multitasking and ensures that programs can run smoothly even with limited memory resources.'],
                    ['content' => 'File management is another critical function of an OS. It organizes data into directories and files, providing a structured way to store and retrieve information. Common file system formats include FAT, NTFS, ext4, and HFS+, each designed for different operating environments.'],
                    ['content' => 'Operating systems also play a vital role in security, enforcing user authentication, access control, and encryption mechanisms. They prevent unauthorized access and protect sensitive data from cyber threats such as malware and hacking attempts.'],
                    ['content' => 'Graphical User Interfaces (GUIs) have made operating systems more accessible to non-technical users. Early OS relied on Command-Line Interfaces (CLI), where users had to type commands manually. Modern OS provide intuitive GUIs, making them easier to navigate.'],
                    ['content' => 'The evolution of operating systems continues with advancements such as cloud-based OS, which allow users to access their desktop environment from any device. The integration of artificial intelligence in OS is also improving automation, enhancing performance, and optimizing resource allocation.'],
                ],
                'activity' => [
                    ['title' => 'Quiz 1', 'question' => 'Which component of an OS manages hardware resources?', 'correct_answer' => 'Kernel', 'difficulty' => 'novice']
                ]
            ],
            //
            [
                'module_id' => 3, // Computer Programming 1 -> Syntax and Semantics
                'name' => 'Syntax and Semantics',
                'data' => [
                    ['content' => 'Syntax refers to the set of rules that define how programs must be written in a particular programming language. It includes the correct arrangement of symbols, keywords, and expressions to ensure the code is readable and executable by a compiler or interpreter.'],
                    ['content' => 'Semantics, on the other hand, refers to the meaning behind the written code. Even if the syntax is correct, the code must also produce the intended result based on the logic applied.'],
                    ['content' => 'Programming languages enforce strict syntax rules. A misplaced semicolon, incorrect indentation, or missing brackets can result in syntax errors, preventing the program from running.'],
                    ['content' => 'Semantics errors occur when a program runs but produces unintended results due to logical mistakes. Unlike syntax errors, these are harder to detect because the program doesn’t necessarily crash.'],
                    ['content' => 'High-level programming languages such as Python, Java, and C++ have unique syntax structures. For example, Python uses indentation to define code blocks, whereas C++ relies on curly brackets `{}`.'],
                    ['content' => 'Compilers and interpreters check for syntax errors before executing a program. A compiler translates the entire code into machine language before running it, while an interpreter executes code line by line.'],
                    ['content' => 'Programming conventions, such as proper indentation and naming standards, improve readability and maintainability. These conventions ensure that code can be easily understood by other developers.'],
                    ['content' => 'Understanding syntax and semantics helps programmers write error-free code, reducing debugging time. Mastering these concepts leads to efficient problem-solving in software development.'],
                    ['content' => 'Learning a language’s syntax first is essential, but writing meaningful and efficient code requires a deep understanding of its semantics.'],
                    ['content' => 'Practical exercises, such as writing small programs with correct syntax and testing logical conditions, help reinforce these fundamental programming concepts.'],
                ],
                'activity' => [
                    ['title' => 'Syntax Quiz', 'question' => 'What is the difference between syntax and semantics?', 'correct_answer' => 'Syntax defines structure, semantics defines meaning.', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 4, // Computer Programming 1 -> Control Structures
                'name' => 'Control Structures',
                'data' => [
                    ['content' => 'Control structures determine the flow of execution in a program. They allow programmers to define how and when specific parts of the code should execute.'],
                    ['content' => 'The three main types of control structures are sequence, selection, and iteration. Sequence structures execute statements in order, selection structures make decisions, and iteration structures loop through code blocks.'],
                    ['content' => 'Conditional statements, such as `if`, `else`, and `switch`, enable decision-making in programs. These structures evaluate conditions and execute different code blocks based on the results.'],
                    ['content' => 'Looping structures, including `for`, `while`, and `do-while` loops, allow repetitive execution of code. These loops continue running until a specific condition is met.'],
                    ['content' => 'Nested control structures involve placing one control structure inside another. This is useful for complex decision-making and repetitive tasks.'],
                    ['content' => 'Break and continue statements control loop execution. `Break` exits a loop prematurely, while `continue` skips the current iteration and moves to the next one.'],
                    ['content' => 'Infinite loops occur when a loop’s exit condition is never met. These can crash programs or cause high CPU usage if not handled properly.'],
                    ['content' => 'Using control structures effectively improves code efficiency. Properly designed loops and conditions minimize unnecessary computations, optimizing performance.'],
                    ['content' => 'Many programming languages offer variations of control structures. For example, Python has list comprehensions that act as inline loops, while JavaScript supports the `switch-case` structure for multi-branch decisions.'],
                    ['content' => 'Practicing control structures through coding exercises, such as implementing simple calculators or menu-driven programs, enhances programming skills and logical thinking.'],
                ],
                'activity' => [
                    ['title' => 'Looping Quiz', 'question' => 'Which loop guarantees at least one execution?', 'correct_answer' => 'do-while loop', 'difficulty' => 'novice']
                ]
            ],
            //
            [
                'module_id' => 5, // Discrete Mathematics -> Logic and Proofs
                'name' => 'Logic and Proofs',
                'data' => [
                    ['content' => 'Logic forms the foundation of mathematical reasoning. It involves statements, truth values, and logical operations to construct valid arguments.'],
                    ['content' => 'Propositional logic uses simple statements combined with logical operators such as AND, OR, and NOT to form complex expressions.'],
                    ['content' => 'Predicate logic extends propositional logic by introducing variables and quantifiers like "for all" (∀) and "there exists" (∃).'],
                    ['content' => 'Logical equivalence occurs when two statements always have the same truth value, regardless of variable assignments. De Morgan’s laws help transform logical expressions into equivalent forms.'],
                    ['content' => 'A proof is a step-by-step argument that verifies the truth of a mathematical statement. Common proof techniques include direct proof, proof by contradiction, and proof by induction.'],
                    ['content' => 'Direct proofs involve assuming the hypothesis and logically deriving the conclusion. They are straightforward but require precise logical steps.'],
                    ['content' => 'Proof by contradiction assumes the opposite of the statement to be proven and demonstrates that this assumption leads to a contradiction.'],
                    ['content' => 'Proof by induction is a powerful method for proving statements involving natural numbers. It involves proving a base case and an inductive step.'],
                    ['content' => 'Logical reasoning is essential in computer science, particularly in fields like artificial intelligence, programming, and algorithm design.'],
                    ['content' => 'Applying logic in real-world scenarios, such as designing circuits or writing error-free code, demonstrates its practical significance.'],
                ],
                'activity' => [
                    ['title' => 'Logical Reasoning Quiz', 'question' => 'What proof method assumes the opposite of what you want to prove?', 'correct_answer' => 'Proof by contradiction', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 6, // Discrete Mathematics -> Set Theory
                'name' => 'Set Theory',
                'data' => [
                    ['content' => 'Set theory is the study of collections of objects, called sets, and the relationships between them. It forms the basis of modern mathematics.'],
                    ['content' => 'A set is a well-defined collection of distinct objects. These objects, called elements, can be numbers, symbols, or even other sets.'],
                    ['content' => 'Common set operations include union (∪), intersection (∩), and difference (-). These operations allow manipulation and comparison of sets.'],
                    ['content' => 'The universal set contains all elements under consideration, while the empty set (∅) contains no elements.'],
                    ['content' => 'A subset is a set whose elements are all contained within another set. If two sets have the same elements, they are called equal sets.'],
                    ['content' => 'Venn diagrams visually represent set relationships, making it easier to understand intersections, unions, and differences.'],
                    ['content' => 'The Cartesian product of two sets produces ordered pairs, used in database relations and coordinate geometry.'],
                    ['content' => 'Set theory provides the foundation for functions, relations, and probability theory in mathematics and computer science.'],
                    ['content' => 'Understanding finite and infinite sets is crucial in complexity analysis, where algorithms are evaluated based on input sizes.'],
                    ['content' => 'Set theory is widely used in database management, data science, and artificial intelligence, where data classification and relationships are essential.'],
                ],
                'activity' => [
                    ['title' => 'Set Theory Quiz', 'question' => 'What is the set that contains no elements?', 'correct_answer' => 'Empty set (∅)', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 7, // Discrete Mathematics -> Graph Theory
                'name' => 'Graph Theory',
                'data' => [
                    ['content' => 'Graph theory is the study of graphs, which are mathematical structures used to model pairwise relationships between objects.'],
                    ['content' => 'A graph consists of vertices (nodes) connected by edges (links). Graphs are widely used in computer science, social networks, and logistics.'],
                    ['content' => 'Graphs can be classified as directed or undirected. In directed graphs, edges have a direction, whereas in undirected graphs, they do not.'],
                    ['content' => 'A path in a graph is a sequence of edges that connect a sequence of vertices. If a path starts and ends at the same vertex, it forms a cycle.'],
                    ['content' => 'A tree is a special type of graph with no cycles and a hierarchical structure. Trees are used in data structures like binary search trees.'],
                    ['content' => 'Graph traversal algorithms, such as Depth-First Search (DFS) and Breadth-First Search (BFS), help explore graphs systematically.'],
                    ['content' => 'Graph coloring is an important concept in scheduling, map coloring, and resource allocation problems.'],
                    ['content' => 'Shortest path algorithms, such as Dijkstra’s and Bellman-Ford, are crucial for navigation and network routing.'],
                    ['content' => 'Graphs are used in real-world applications like social networks (e.g., Facebook connections), transportation networks (e.g., Google Maps), and recommendation systems (e.g., Netflix suggestions).'],
                    ['content' => 'Studying graph theory enhances problem-solving skills and helps in understanding complex networks in various fields, including cybersecurity and artificial intelligence.'],
                ],
                'activity' => [
                    ['title' => 'Graph Theory Quiz', 'question' => 'Which algorithm finds the shortest path in a weighted graph?', 'correct_answer' => 'Dijkstra’s Algorithm', 'difficulty' => 'novice']
                ]
            ],
            //
            [
                'module_id' => 8, // Digital Logic Design -> Boolean Algebra
                'name' => 'Boolean Algebra',
                'data' => [
                    ['content' => 'Boolean algebra is a mathematical system used for analyzing and simplifying digital logic circuits. It is based on binary values: 0 (false) and 1 (true).'],
                    ['content' => 'The fundamental operations in Boolean algebra include AND, OR, and NOT, which correspond to multiplication, addition, and negation in standard algebra.'],
                    ['content' => 'Boolean expressions can be simplified using algebraic identities such as De Morgan’s Theorems, the Idempotent Law, and the Absorption Law.'],
                    ['content' => 'Truth tables are used to represent the output of Boolean functions based on all possible input values.'],
                    ['content' => 'Karnaugh Maps (K-maps) provide a visual method to simplify Boolean expressions by grouping adjacent ones to reduce complexity.'],
                    ['content' => 'The Sum of Products (SOP) and Product of Sums (POS) forms are two standard ways of representing Boolean expressions.'],
                    ['content' => 'Logic gates implement Boolean functions in digital circuits. Basic gates include AND, OR, and NOT, while universal gates include NAND and NOR.'],
                    ['content' => 'NAND and NOR gates are considered universal because they can be used to create any other logic gate.'],
                    ['content' => 'Boolean algebra is essential in circuit design, programming conditions, and artificial intelligence decision-making.'],
                    ['content' => 'Modern applications of Boolean algebra include digital circuit design, microprocessor design, and error detection systems in data transmission.'],
                ],
                'activity' => [
                    ['title' => 'Boolean Logic Quiz', 'question' => 'Which gate is considered a universal gate?', 'correct_answer' => 'NAND', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 9, // Digital Logic Design -> Combinational Circuits
                'name' => 'Combinational Circuits',
                'data' => [
                    ['content' => 'Combinational circuits are logic circuits whose output depends only on the current inputs, without any memory element.'],
                    ['content' => 'Basic combinational circuits include adders, subtractors, multiplexers, demultiplexers, encoders, and decoders.'],
                    ['content' => 'An adder is a circuit that performs binary addition. The half-adder adds two single-bit numbers, while the full-adder adds three bits and includes a carry input.'],
                    ['content' => 'Multiplexers (MUX) select one input from multiple data inputs based on selector inputs. They are widely used in communication systems.'],
                    ['content' => 'Decoders convert binary information into a more usable form. A 2-to-4 decoder, for example, takes two input bits and activates one of four outputs.'],
                    ['content' => 'Encoders perform the reverse function of decoders by converting multiple input signals into a smaller number of output bits.'],
                    ['content' => 'Arithmetic Logic Units (ALUs) use combinational circuits to perform arithmetic and logical operations in processors.'],
                    ['content' => 'Combinational circuits are used in calculators, data transmission, and digital signal processing applications.'],
                    ['content' => 'The main drawback of combinational circuits is their lack of memory, which limits their ability to store previous states.'],
                    ['content' => 'Minimization techniques such as Karnaugh Maps (K-maps) and Quine-McCluskey methods help simplify combinational circuit designs.'],
                ],
                'activity' => [
                    ['title' => 'Combinational Circuit Quiz', 'question' => 'Which circuit selects one input from multiple data inputs?', 'correct_answer' => 'Multiplexer', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 10, // Digital Logic Design -> Sequential Circuits
                'name' => 'Sequential Circuits',
                'data' => [
                    ['content' => 'Sequential circuits differ from combinational circuits because they have memory elements that store past inputs and states.'],
                    ['content' => 'The two primary types of sequential circuits are synchronous and asynchronous. Synchronous circuits operate with a clock signal, while asynchronous circuits do not.'],
                    ['content' => 'Flip-flops are the basic memory elements used in sequential circuits. Common types include SR flip-flop, D flip-flop, JK flip-flop, and T flip-flop.'],
                    ['content' => 'A register is a group of flip-flops used to store multiple bits of data temporarily. They are used in microprocessors for instruction execution.'],
                    ['content' => 'Counters are sequential circuits that count pulses. They can be classified as up-counters, down-counters, or up/down counters.'],
                    ['content' => 'State diagrams represent sequential circuits visually, showing how states transition based on inputs.'],
                    ['content' => 'Finite State Machines (FSMs) are models used to design sequential logic systems, widely applied in AI and automation.'],
                    ['content' => 'Sequential circuits are essential for implementing memory units, microcontrollers, and real-time control systems.'],
                    ['content' => 'Clock signals synchronize sequential circuits, ensuring that all components change state at the same time.'],
                    ['content' => 'Applications of sequential circuits include digital clocks, traffic light controllers, and data storage systems.'],
                ],
                'activity' => [
                    ['title' => 'Sequential Circuit Quiz', 'question' => 'Which basic memory element is used in sequential circuits?', 'correct_answer' => 'Flip-flop', 'difficulty' => 'novice']
                ]
            ],
            //
            [
                'module_id' => 11, // Human-Computer Interaction -> Usability Principles
                'name' => 'Usability Principles',
                'data' => [
                    ['content' => 'Usability principles are guidelines used to design interactive systems that are easy to use and efficient for users.'],
                    ['content' => 'One key usability principle is **learnability**, which ensures that users can quickly understand how to use a system without extensive training.'],
                    ['content' => '**Efficiency** refers to how quickly and effectively users can accomplish their tasks using a system.'],
                    ['content' => 'The **error prevention** principle focuses on designing systems that minimize user mistakes and provide clear guidance to recover from errors.'],
                    ['content' => '**Feedback and visibility** are crucial, as users need real-time responses from the system to confirm that their actions are recognized.'],
                    ['content' => '**Flexibility** in design allows different user interaction styles, such as keyboard shortcuts and touch gestures.'],
                    ['content' => '**Simplicity** and **consistency** in the interface improve usability by reducing cognitive load and making navigation intuitive.'],
                    ['content' => 'Jakob Nielsen’s **10 Usability Heuristics** are widely used in HCI to guide effective interface design.'],
                    ['content' => 'Usability testing methods, such as A/B testing and eye-tracking, help refine designs based on real user feedback.'],
                    ['content' => 'Good usability leads to higher user satisfaction, better engagement, and improved productivity in digital applications.'],
                ],
                'activity' => [
                    ['title' => 'Usability Quiz', 'question' => 'Which usability principle ensures that users can recover easily from mistakes?', 'correct_answer' => 'Error prevention', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 12, // Human-Computer Interaction -> User-Centered Design
                'name' => 'User-Centered Design',
                'data' => [
                    ['content' => 'User-Centered Design (UCD) is an approach that places users at the core of the design process, ensuring that products meet their needs and preferences.'],
                    ['content' => 'A key step in UCD is conducting **user research**, which includes surveys, interviews, and observational studies to understand user behavior.'],
                    ['content' => '**Personas** are fictional representations of target users, helping designers anticipate their goals, frustrations, and needs.'],
                    ['content' => 'User journey mapping helps visualize the different stages a user goes through while interacting with a system.'],
                    ['content' => 'Wireframing and prototyping allow designers to create and test early versions of interfaces before final development.'],
                    ['content' => '**Usability testing** is an essential phase in UCD, where real users interact with the system and provide feedback.'],
                    ['content' => 'Iterative design is a key concept in UCD, ensuring continuous improvements based on user feedback.'],
                    ['content' => 'Accessibility considerations, such as text-to-speech support and high-contrast modes, are vital in UCD to accommodate diverse users.'],
                    ['content' => 'Successful UCD results in better usability, reduced user frustration, and a more positive overall experience.'],
                    ['content' => 'Companies like Apple and Google apply UCD principles to create user-friendly products that dominate the market.'],
                ],
                'activity' => [
                    ['title' => 'UCD Quiz', 'question' => 'What is the main goal of User-Centered Design?', 'correct_answer' => 'To prioritize user needs and usability', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 13, // Human-Computer Interaction -> Cognitive Models
                'name' => 'Cognitive Models',
                'data' => [
                    ['content' => 'Cognitive models in HCI represent how users think, process information, and interact with digital interfaces.'],
                    ['content' => 'The **GOMS Model** (Goals, Operators, Methods, Selection rules) predicts how long it will take a user to complete a task based on mental processes.'],
                    ['content' => 'Fitts’ Law states that the time required to move to a target depends on its size and distance, impacting UI design for buttons and links.'],
                    ['content' => 'Hick’s Law explains that decision time increases with the number of choices, emphasizing the importance of simple navigation.'],
                    ['content' => 'The **Mental Model** concept describes how users form expectations about how a system should work based on past experiences.'],
                    ['content' => 'Cognitive Load Theory suggests that reducing unnecessary cognitive effort in design improves usability and retention.'],
                    ['content' => 'User memory limitations play a role in design, as interfaces should minimize reliance on recall and instead support recognition.'],
                    ['content' => 'Predictive models like the Keystroke-Level Model (KLM) estimate task execution time based on user interactions.'],
                    ['content' => 'Cognitive models are used to enhance interface designs in fields like gaming, e-learning, and virtual reality.'],
                    ['content' => 'Understanding human cognition helps designers create intuitive and user-friendly applications.'],
                ],
                'activity' => [
                    ['title' => 'Cognitive Models Quiz', 'question' => 'Which law states that increasing choices increases decision time?', 'correct_answer' => 'Hick’s Law', 'difficulty' => 'novice']
                ]
            ],
            //
            [
                'module_id' => 14, // Data Structures and Algorithms -> Linked Lists
                'name' => 'Linked Lists',
                'data' => [
                    ['content' => 'A linked list is a linear data structure where elements are stored in nodes, each containing a data field and a reference (pointer) to the next node.'],
                    ['content' => 'Unlike arrays, linked lists do not have a fixed size and can grow dynamically by allocating memory as needed.'],
                    ['content' => 'There are different types of linked lists: **Singly Linked Lists**, **Doubly Linked Lists**, and **Circular Linked Lists**.'],
                    ['content' => 'In a **Singly Linked List**, each node has a pointer to the next node, but no reference to the previous node.'],
                    ['content' => 'A **Doubly Linked List** allows traversal in both directions as each node contains pointers to both the next and previous nodes.'],
                    ['content' => 'A **Circular Linked List** forms a loop by connecting the last node back to the first, useful for applications like task scheduling.'],
                    ['content' => 'Common operations on linked lists include insertion, deletion, traversal, and searching, with time complexity varying based on position.'],
                    ['content' => 'Linked lists are used in dynamic memory allocation, undo functionality in applications, and implementing stacks and queues.'],
                    ['content' => 'The main disadvantage of linked lists compared to arrays is the extra memory required for storing pointers and slower access time.'],
                    ['content' => 'Understanding linked lists is fundamental for learning more advanced data structures like trees and graphs.'],
                ],
                'activity' => [
                    ['title' => 'Linked List Quiz', 'question' => 'Which type of linked list allows traversal in both directions?', 'correct_answer' => 'Doubly Linked List', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 15, // Data Structures and Algorithms -> Sorting Algorithms
                'name' => 'Sorting Algorithms',
                'data' => [
                    ['content' => 'Sorting algorithms are used to arrange data in a specific order, improving efficiency for searching and retrieval operations.'],
                    ['content' => 'There are two main types of sorting: **Comparison-based sorting** and **Non-comparison sorting**.'],
                    ['content' => 'Common **comparison-based sorting algorithms** include Bubble Sort, Insertion Sort, Selection Sort, Merge Sort, Quick Sort, and Heap Sort.'],
                    ['content' => 'Bubble Sort repeatedly swaps adjacent elements if they are in the wrong order, making it inefficient for large datasets.'],
                    ['content' => 'Quick Sort uses a divide-and-conquer approach, selecting a pivot element and partitioning the array around it.'],
                    ['content' => 'Merge Sort is a stable sorting algorithm that splits an array into smaller subarrays, sorts them, and merges them back.'],
                    ['content' => 'Heap Sort is based on the binary heap data structure, providing an efficient way to sort elements with **O(n log n)** time complexity.'],
                    ['content' => 'Counting Sort and Radix Sort are examples of **non-comparison sorting algorithms**, often faster for specific datasets.'],
                    ['content' => 'Sorting is essential in many applications, such as database indexing, searching algorithms, and real-time analytics.'],
                    ['content' => 'Choosing the right sorting algorithm depends on factors like dataset size, stability requirements, and available memory.'],
                ],
                'activity' => [
                    ['title' => 'Sorting Algorithm Quiz', 'question' => 'Which sorting algorithm is based on the divide-and-conquer technique?', 'correct_answer' => 'Merge Sort', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 16, // Data Structures and Algorithms -> Graph Algorithms
                'name' => 'Graph Algorithms',
                'data' => [
                    ['content' => 'Graph algorithms help solve problems related to network structures, social connections, and pathfinding.'],
                    ['content' => 'A graph consists of **vertices (nodes)** and **edges (connections between nodes)**, used to represent relationships.'],
                    ['content' => 'There are two types of graphs: **Directed Graphs (where edges have direction)** and **Undirected Graphs (where edges have no direction)**.'],
                    ['content' => '**Breadth-First Search (BFS)** explores nodes level by level, useful for shortest path problems and web crawling.'],
                    ['content' => '**Depth-First Search (DFS)** explores nodes deeply before backtracking, commonly used for maze-solving and cycle detection.'],
                    ['content' => 'Dijkstra’s Algorithm finds the shortest path between a source node and all other nodes in a weighted graph.'],
                    ['content' => 'The **A* Algorithm** improves upon Dijkstra’s by using heuristics, making it more efficient in pathfinding applications.'],
                    ['content' => 'Minimum spanning tree algorithms like **Kruskal’s Algorithm** and **Prim’s Algorithm** are used to optimize network connections.'],
                    ['content' => 'Graphs are widely used in navigation systems, social networks, recommendation engines, and network routing.'],
                    ['content' => 'Understanding graph algorithms is crucial for applications in artificial intelligence, machine learning, and optimization problems.'],
                ],
                'activity' => [
                    ['title' => 'Graph Algorithm Quiz', 'question' => 'Which algorithm is commonly used to find the shortest path in a weighted graph?', 'correct_answer' => 'Dijkstra’s Algorithm', 'difficulty' => 'novice']
                ]
            ],
            //
            [
                'module_id' => 17, // Computer Programming 2 -> OOP Principles
                'name' => 'OOP Principles',
                'data' => [
                    ['content' => 'Object-Oriented Programming (OOP) is a programming paradigm that organizes software design around objects rather than functions.'],
                    ['content' => 'The four fundamental OOP principles are **Encapsulation, Abstraction, Inheritance, and Polymorphism**.'],
                    ['content' => '**Encapsulation** is the process of wrapping data and methods into a single unit (class) and restricting direct access to some components.'],
                    ['content' => '**Abstraction** focuses on hiding complex implementation details while exposing only essential features.'],
                    ['content' => '**Inheritance** allows a class (child) to derive properties and behaviors from another class (parent), reducing code duplication.'],
                    ['content' => '**Polymorphism** enables objects to be treated as instances of their parent class, supporting method overriding and overloading.'],
                    ['content' => 'OOP improves code reusability, maintainability, and scalability in software development.'],
                    ['content' => 'Languages like Java, C++, Python, and PHP support OOP, making it widely used in application development.'],
                    ['content' => 'Design patterns such as Singleton, Factory, and Observer rely heavily on OOP principles.'],
                    ['content' => 'Understanding OOP is crucial for developing complex software systems, including enterprise applications and game development.'],
                ],
                'activity' => [
                    ['title' => 'OOP Quiz', 'question' => 'Which OOP principle allows one class to inherit properties from another?', 'correct_answer' => 'Inheritance', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 18, // Computer Programming 2 -> Exception Handling
                'name' => 'Exception Handling',
                'data' => [
                    ['content' => 'Exception handling is a mechanism used to handle runtime errors and ensure the smooth execution of a program.'],
                    ['content' => 'Exceptions occur due to issues like invalid user input, division by zero, file not found, or database connection failures.'],
                    ['content' => 'Most programming languages use **try-catch blocks** to handle exceptions gracefully.'],
                    ['content' => 'The **try block** contains the code that may cause an exception, while the **catch block** handles the exception when it occurs.'],
                    ['content' => 'Custom exceptions can be defined to handle specific error scenarios in an application.'],
                    ['content' => 'A **finally block** is used to execute code regardless of whether an exception occurs or not, such as closing files or database connections.'],
                    ['content' => 'Unchecked exceptions (runtime errors) occur due to logical mistakes, while checked exceptions must be explicitly handled.'],
                    ['content' => 'Proper exception handling enhances application reliability, preventing unexpected crashes and improving debugging.'],
                    ['content' => 'Logging exceptions in error logs helps developers track and resolve issues effectively.'],
                    ['content' => 'Exception handling is widely used in web applications, APIs, and financial systems to maintain system stability.'],
                ],
                'activity' => [
                    ['title' => 'Exception Handling Quiz', 'question' => 'Which block is used to execute code regardless of an exception occurring?', 'correct_answer' => 'finally block', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 19, // Computer Programming 2 -> Multithreading
                'name' => 'Multithreading',
                'data' => [
                    ['content' => 'Multithreading is the ability of a CPU to execute multiple threads concurrently, improving application performance.'],
                    ['content' => 'A thread is a lightweight process that runs independently, sharing resources like memory and CPU time.'],
                    ['content' => 'Multithreading enables tasks such as background computations, UI responsiveness, and parallel processing.'],
                    ['content' => 'Thread synchronization is crucial to prevent race conditions and ensure data consistency when multiple threads access shared resources.'],
                    ['content' => 'Deadlocks occur when two or more threads wait for each other indefinitely, causing the application to freeze.'],
                    ['content' => 'Thread pools manage a collection of reusable threads to optimize resource allocation in high-performance applications.'],
                    ['content' => 'Multithreading is widely used in gaming, web servers, real-time systems, and operating systems.'],
                    ['content' => 'In Java, threads are implemented using the **Thread class** or **Runnable interface**.'],
                    ['content' => 'Thread priority determines the execution order of threads, but it is not always guaranteed due to the CPU scheduler.'],
                    ['content' => 'Multithreading enhances efficiency but requires careful handling to avoid concurrency issues like data corruption.'],
                ],
                'activity' => [
                    ['title' => 'Multithreading Quiz', 'question' => 'What is a potential issue when multiple threads access shared data without synchronization?', 'correct_answer' => 'Race condition', 'difficulty' => 'novice']
                ]
            ],
            //
            [
                'module_id' => 20, // Operating Systems -> Memory Management
                'name' => 'Memory Management',
                'data' => [
                    ['content' => 'Memory management is a crucial function of an operating system that handles the allocation and deallocation of memory for processes.'],
                    ['content' => 'The OS ensures that processes get the required memory without interfering with each other.'],
                    ['content' => 'Paging is a technique used to manage memory by dividing it into fixed-size pages, reducing fragmentation.'],
                    ['content' => 'Segmentation divides memory into variable-sized segments based on logical divisions such as functions or arrays.'],
                    ['content' => 'Virtual memory allows a system to use disk space as an extension of RAM, enabling the execution of large programs.'],
                    ['content' => 'Swapping involves temporarily moving processes in and out of memory to optimize performance.'],
                    ['content' => 'The OS maintains a memory management unit (MMU) to translate virtual addresses to physical addresses.'],
                    ['content' => 'Memory leaks occur when programs do not release allocated memory, leading to system slowdowns.'],
                    ['content' => 'Efficient memory management improves multitasking and system stability.'],
                    ['content' => 'Security features like memory protection prevent one process from accessing another process’s memory.'],
                ],
                'activity' => [
                    ['title' => 'Memory Management Quiz', 'question' => 'Which memory management technique divides memory into fixed-size pages?', 'correct_answer' => 'Paging', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 21, // Operating Systems -> Process Scheduling
                'name' => 'Process Scheduling',
                'data' => [
                    ['content' => 'Process scheduling is a core function of an OS that manages the execution of processes on the CPU.'],
                    ['content' => 'The scheduler determines which process gets CPU time and when.'],
                    ['content' => 'There are three types of scheduling: **long-term, short-term, and medium-term scheduling**.'],
                    ['content' => '**Long-term scheduling** selects which processes should enter the ready queue from the job pool.'],
                    ['content' => '**Short-term scheduling** decides which process to execute next from the ready queue.'],
                    ['content' => '**Medium-term scheduling** temporarily removes processes from memory to free up space (swapping).'],
                    ['content' => 'Common scheduling algorithms include **FCFS, SJF, Round Robin, and Priority Scheduling**.'],
                    ['content' => 'Preemptive scheduling allows the OS to interrupt a running process, whereas non-preemptive scheduling does not.'],
                    ['content' => 'The goal of scheduling is to maximize CPU utilization and reduce response time.'],
                    ['content' => 'Fair scheduling ensures that all processes get a fair share of CPU time, preventing starvation.'],
                ],
                'activity' => [
                    ['title' => 'Process Scheduling Quiz', 'question' => 'Which scheduling algorithm assigns a fixed time slice to each process in a cyclic order?', 'correct_answer' => 'Round Robin', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 22, // Operating Systems -> File Systems
                'name' => 'File Systems',
                'data' => [
                    ['content' => 'A file system is responsible for organizing, storing, and retrieving data on a storage device.'],
                    ['content' => 'The OS provides a structured way to access and manage files efficiently.'],
                    ['content' => 'Common file systems include **NTFS, FAT32, ext4, HFS+, and APFS**.'],
                    ['content' => 'A file system consists of directories, files, and metadata that describe file attributes.'],
                    ['content' => 'The OS maintains a file allocation table (FAT) or index nodes (inodes) to track file locations.'],
                    ['content' => 'File permissions and access control lists (ACLs) ensure security by restricting unauthorized access.'],
                    ['content' => 'Journaling file systems like NTFS and ext4 prevent data corruption by keeping track of changes before committing them.'],
                    ['content' => 'Mounting and unmounting allow file systems to be attached or detached from the OS dynamically.'],
                    ['content' => 'Disk fragmentation occurs when files are stored in non-contiguous locations, reducing performance.'],
                    ['content' => 'Efficient file systems improve data retrieval speed and overall system performance.'],
                ],
                'activity' => [
                    ['title' => 'File Systems Quiz', 'question' => 'Which file system is commonly used in Linux distributions?', 'correct_answer' => 'ext4', 'difficulty' => 'novice']
                ]
            ],
            //
            [
                'module_id' => 23, // Database Management Systems -> SQL Basics
                'name' => 'SQL Basics',
                'data' => [
                    ['content' => 'Structured Query Language (SQL) is the standard language for interacting with relational databases.'],
                    ['content' => 'SQL allows users to create, retrieve, update, and delete records from a database efficiently.'],
                    ['content' => 'The most commonly used SQL commands are **SELECT, INSERT, UPDATE, DELETE**.'],
                    ['content' => 'A **SELECT** statement retrieves data from one or more tables using conditions.'],
                    ['content' => 'The **WHERE** clause is used to filter results based on specified conditions.'],
                    ['content' => 'Joins in SQL allow the combination of data from multiple tables based on related columns.'],
                    ['content' => 'Indexes improve query performance by allowing faster lookups on large datasets.'],
                    ['content' => 'Stored procedures and functions enhance efficiency by encapsulating frequently used queries.'],
                    ['content' => 'SQL supports aggregate functions like COUNT, SUM, AVG, MIN, and MAX for data analysis.'],
                    ['content' => 'Transactions ensure database integrity by following ACID (Atomicity, Consistency, Isolation, Durability) principles.'],
                ],
                'activity' => [
                    ['title' => 'SQL Query Challenge', 'question' => 'Write an SQL query to fetch all records from a table named "users" where the age is greater than 25.', 'correct_answer' => 'SELECT * FROM users WHERE age > 25;', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 24, // Database Management Systems -> Normalization
                'name' => 'Normalization',
                'data' => [
                    ['content' => 'Normalization is a database design technique that organizes data to reduce redundancy and improve integrity.'],
                    ['content' => 'It divides large tables into smaller ones while establishing relationships between them using foreign keys.'],
                    ['content' => 'The primary objective of normalization is to eliminate duplicate data and ensure consistency.'],
                    ['content' => 'Normalization follows a series of forms: **1NF, 2NF, 3NF, BCNF, 4NF, and 5NF**.'],
                    ['content' => '**First Normal Form (1NF):** Ensures that all columns contain atomic (indivisible) values.'],
                    ['content' => '**Second Normal Form (2NF):** Eliminates partial dependencies by ensuring that each non-key attribute is fully dependent on the primary key.'],
                    ['content' => '**Third Normal Form (3NF):** Removes transitive dependencies, ensuring that attributes depend only on the primary key.'],
                    ['content' => '**Boyce-Codd Normal Form (BCNF):** Further refines 3NF by addressing anomalies related to candidate keys.'],
                    ['content' => 'Higher normal forms (4NF, 5NF) address advanced redundancy issues in complex databases.'],
                    ['content' => 'Normalization enhances database efficiency but may require additional joins for complex queries.'],
                ],
                'activity' => [
                    ['title' => 'Normalization Exercise', 'question' => 'Which normal form ensures that no partial dependencies exist?', 'correct_answer' => '2NF', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 25, // Database Management Systems -> Indexing
                'name' => 'Indexing',
                'data' => [
                    ['content' => 'Indexing is a technique used in databases to optimize query performance by enabling faster data retrieval.'],
                    ['content' => 'An index is a data structure that improves the speed of data searches without scanning the entire table.'],
                    ['content' => 'Indexes work similarly to a book’s table of contents, allowing quick access to specific records.'],
                    ['content' => 'Types of indexes include **primary index, unique index, composite index, and full-text index**.'],
                    ['content' => '**B-Tree indexes** are commonly used in relational databases for fast lookups.'],
                    ['content' => '**Hash indexes** are efficient for key-based lookups but not suitable for range queries.'],
                    ['content' => 'Clustered indexes define the physical order of data storage, while non-clustered indexes store a separate lookup table.'],
                    ['content' => 'Proper indexing can significantly enhance the performance of large-scale applications.'],
                    ['content' => 'However, excessive indexing can slow down insert and update operations.'],
                    ['content' => 'Best practices include indexing frequently queried columns and avoiding redundant indexes.'],
                ],
                'activity' => [
                    ['title' => 'Indexing Quiz', 'question' => 'Which index type defines the physical order of data in a table?', 'correct_answer' => 'Clustered Index', 'difficulty' => 'expert']
                ]
            ],
            //
            [
                'module_id' => 26, // Object-Oriented Programming -> Classes and Objects
                'name' => 'Classes and Objects',
                'data' => [
                    ['content' => 'Object-Oriented Programming (OOP) is a paradigm that organizes code into objects representing real-world entities.'],
                    ['content' => 'A class is a blueprint for creating objects, defining attributes (properties) and behaviors (methods).'],
                    ['content' => 'An object is an instance of a class with its own values for the defined attributes.'],
                    ['content' => 'Encapsulation ensures that data is hidden from unauthorized access and can only be modified through predefined methods.'],
                    ['content' => 'Constructors are special methods used to initialize objects when they are created.'],
                    ['content' => 'Methods define the behavior of objects and can be public, private, or protected.'],
                    ['content' => 'Objects interact with each other through method calls, making code reusable and modular.'],
                    ['content' => 'Static methods belong to a class rather than an instance and can be called without creating an object.'],
                    ['content' => 'Properly structuring classes and objects improves code maintainability and readability.'],
                    ['content' => 'OOP is widely used in languages like Java, Python, C++, and PHP to build scalable applications.'],
                ],
                'activity' => [
                    ['title' => 'Classes & Objects Coding Task', 'question' => 'Create a class "Car" with properties "brand" and "model" and a method "drive()".', 'correct_answer' => 'PHP Code: class Car { public $brand; public $model; public function drive() { echo "Driving"; } }', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 27, // Object-Oriented Programming -> Inheritance
                'name' => 'Inheritance',
                'data' => [
                    ['content' => 'Inheritance is an OOP concept that allows a class to inherit properties and methods from another class.'],
                    ['content' => 'The base class (parent class) provides common functionality that derived classes (child classes) can extend.'],
                    ['content' => 'Inheritance promotes code reusability and hierarchical relationships between classes.'],
                    ['content' => 'A child class can override parent methods to implement specialized behavior.'],
                    ['content' => 'In languages like Java and C++, the "extends" keyword is used for inheritance.'],
                    ['content' => 'Multiple inheritance allows a class to inherit from more than one parent class, though it is not supported in all languages.'],
                    ['content' => 'The super keyword allows access to the parent class’s methods and constructors.'],
                    ['content' => 'Abstract classes provide a template for child classes but cannot be instantiated directly.'],
                    ['content' => 'Interface-based inheritance enables classes to implement multiple behaviors without using multiple inheritance.'],
                    ['content' => 'Proper use of inheritance improves modularity and reduces redundancy in large applications.'],
                ],
                'activity' => [
                    ['title' => 'Inheritance Task', 'question' => 'Define a base class "Animal" and a child class "Dog" that extends "Animal".', 'correct_answer' => 'PHP Code: class Animal { public function sound() { echo "Some sound"; } } class Dog extends Animal { public function sound() { echo "Bark"; } }', 'difficulty' => 'expert']
                ]
            ],
            //
            [
                'module_id' => 28, // Software Engineering -> SDLC Models
                'name' => 'SDLC Models',
                'data' => [
                    ['content' => 'Software Development Life Cycle (SDLC) is a process used to design, develop, and test high-quality software.'],
                    ['content' => 'SDLC provides a structured approach to software development, ensuring efficiency and quality.'],
                    ['content' => 'The major SDLC models include Waterfall, V-Model, Spiral, Incremental, and Agile.'],
                    ['content' => 'The Waterfall model follows a linear sequential approach with distinct phases like Requirements, Design, Implementation, Testing, and Maintenance.'],
                    ['content' => 'The V-Model emphasizes verification and validation at each phase, ensuring early detection of defects.'],
                    ['content' => 'The Spiral model focuses on iterative development and risk analysis, making it suitable for large projects.'],
                    ['content' => 'The Incremental model develops software in smaller, manageable parts, ensuring continuous progress.'],
                    ['content' => 'Agile methodology prioritizes flexibility, collaboration, and customer feedback to ensure adaptive development.'],
                    ['content' => 'Each SDLC model has its advantages and is chosen based on project requirements and constraints.'],
                    ['content' => 'Proper SDLC implementation improves project planning, reduces risks, and ensures timely delivery.'],
                ],
                'activity' => [
                    ['title' => 'SDLC Quiz', 'question' => 'Which SDLC model is best suited for projects with evolving requirements?', 'correct_answer' => 'Agile', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 29, // Software Engineering -> Agile Methodology
                'name' => 'Agile Methodology',
                'data' => [
                    ['content' => 'Agile is a software development approach that promotes iterative progress, collaboration, and adaptability.'],
                    ['content' => 'The Agile Manifesto values individuals, working software, customer collaboration, and adaptability over rigid processes.'],
                    ['content' => 'Popular Agile frameworks include Scrum, Kanban, Lean, and Extreme Programming (XP).'],
                    ['content' => 'Scrum divides work into small, manageable iterations called Sprints, lasting 1-4 weeks.'],
                    ['content' => 'The Scrum team consists of a Product Owner, Scrum Master, and Developers working collaboratively.'],
                    ['content' => 'Daily Stand-up meetings help track progress, address roadblocks, and ensure alignment.'],
                    ['content' => 'Kanban focuses on visualizing workflow, limiting work in progress, and continuous delivery.'],
                    ['content' => 'Agile ensures quick feedback loops, early error detection, and higher customer satisfaction.'],
                    ['content' => 'Agile development requires strong communication, flexibility, and continuous improvement.'],
                    ['content' => 'Compared to Waterfall, Agile is more adaptive and better suited for projects with evolving requirements.'],
                ],
                'activity' => [
                    ['title' => 'Agile Exercise', 'question' => 'What is the primary purpose of a daily stand-up meeting in Scrum?', 'correct_answer' => 'To track progress and address issues', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 30, // Software Engineering -> Software Testing
                'name' => 'Software Testing',
                'data' => [
                    ['content' => 'Software testing is the process of evaluating a system to ensure it meets the specified requirements.'],
                    ['content' => 'Testing identifies defects and ensures software reliability, performance, and security.'],
                    ['content' => 'Types of testing include Unit Testing, Integration Testing, System Testing, and Acceptance Testing.'],
                    ['content' => 'Unit Testing focuses on testing individual components or modules.'],
                    ['content' => 'Integration Testing ensures that combined components work together correctly.'],
                    ['content' => 'System Testing validates the entire application against the defined requirements.'],
                    ['content' => 'Acceptance Testing determines whether the software is ready for deployment.'],
                    ['content' => 'Manual testing requires human effort, while automated testing uses tools like Selenium and JUnit.'],
                    ['content' => 'Regression Testing ensures that new updates do not break existing functionalities.'],
                    ['content' => 'Proper testing reduces errors, enhances security, and improves software quality.'],
                ],
                'activity' => [
                    ['title' => 'Testing Quiz', 'question' => 'Which type of testing checks whether individual modules work as expected?', 'correct_answer' => 'Unit Testing', 'difficulty' => 'expert']
                ]
            ],
            //
            [
                'module_id' => 31, // Computer Networks -> Network Topologies
                'name' => 'Network Topologies',
                'data' => [
                    ['content' => 'A network topology refers to the physical or logical arrangement of nodes in a network.'],
                    ['content' => 'Common network topologies include Bus, Star, Ring, Mesh, and Hybrid.'],
                    ['content' => 'Bus topology connects all devices to a single communication line, but failure in the main cable disrupts the network.'],
                    ['content' => 'Star topology connects each node to a central hub, making it easy to manage but dependent on the hub.'],
                    ['content' => 'Ring topology forms a circular network where each device is connected to two others, but a single failure can disrupt the system.'],
                    ['content' => 'Mesh topology provides multiple paths between nodes, improving reliability but increasing complexity.'],
                    ['content' => 'Hybrid topology combines two or more different topologies for enhanced efficiency.'],
                    ['content' => 'Choosing the right topology depends on factors like cost, scalability, and fault tolerance.'],
                    ['content' => 'Wireless topologies, like WLAN and cellular networks, eliminate the need for physical connections.'],
                    ['content' => 'Understanding topologies helps in designing networks that are efficient and scalable.'],
                ],
                'activity' => [
                    ['title' => 'Topology Quiz', 'question' => 'Which topology connects all devices to a central hub?', 'correct_answer' => 'Star topology', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 32, // Computer Networks -> TCP/IP Model
                'name' => 'TCP/IP Model',
                'data' => [
                    ['content' => 'The TCP/IP model is a framework that standardizes how data is transmitted over networks.'],
                    ['content' => 'It consists of four layers: Application, Transport, Internet, and Network Access.'],
                    ['content' => 'The Application Layer handles user interactions and protocols like HTTP, FTP, and SMTP.'],
                    ['content' => 'The Transport Layer manages end-to-end communication using TCP (reliable) and UDP (faster but less reliable).'],
                    ['content' => 'The Internet Layer is responsible for routing data using the IP protocol.'],
                    ['content' => 'The Network Access Layer controls how data is transmitted physically, using Ethernet and Wi-Fi.'],
                    ['content' => 'The TCP/IP model is widely used for internet communication.'],
                    ['content' => 'IPv4 and IPv6 are addressing schemes used to identify devices in a network.'],
                    ['content' => 'Protocols like DHCP and DNS play essential roles in managing network resources.'],
                    ['content' => 'Understanding TCP/IP helps in designing secure and efficient networks.'],
                ],
                'activity' => [
                    ['title' => 'TCP/IP Challenge', 'question' => 'Which layer of the TCP/IP model is responsible for routing?', 'correct_answer' => 'Internet Layer', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 33, // Computer Networks -> Network Security
                'name' => 'Network Security',
                'data' => [
                    ['content' => 'Network security refers to measures taken to protect data, devices, and networks from cyber threats.'],
                    ['content' => 'Common network security threats include malware, phishing, denial-of-service (DoS) attacks, and hacking.'],
                    ['content' => 'Firewalls monitor and control incoming and outgoing network traffic based on security rules.'],
                    ['content' => 'Encryption secures data by converting it into an unreadable format that requires a key to decode.'],
                    ['content' => 'VPNs (Virtual Private Networks) create secure tunnels for private communication over public networks.'],
                    ['content' => 'Multi-factor authentication (MFA) enhances security by requiring multiple verification methods.'],
                    ['content' => 'Antivirus software detects and removes malicious programs from devices.'],
                    ['content' => 'Regular software updates help fix security vulnerabilities and prevent exploits.'],
                    ['content' => 'Social engineering attacks trick users into revealing confidential information.'],
                    ['content' => 'Strong password policies and cybersecurity awareness training improve network security.'],
                ],
                'activity' => [
                    ['title' => 'Security Awareness', 'question' => 'Which technology encrypts internet traffic for secure communication?', 'correct_answer' => 'VPN', 'difficulty' => 'expert']
                ]
            ],
            //
            [
                'module_id' => 34, // Web Development -> HTML & CSS
                'name' => 'HTML & CSS',
                'data' => [
                    ['content' => 'HTML (HyperText Markup Language) is the backbone of web pages, defining their structure.'],
                    ['content' => 'CSS (Cascading Style Sheets) is used to style HTML elements, improving layout and design.'],
                    ['content' => 'HTML uses tags such as <head>, <body>, <p>, <h1> to <h6>, <a>, <img>, and <div>.'],
                    ['content' => 'CSS properties like color, font-size, margin, padding, and flexbox help control visual presentation.'],
                    ['content' => 'Responsive design uses media queries to adjust layouts for different screen sizes.'],
                    ['content' => 'Semantic HTML improves accessibility and SEO by using elements like <article>, <section>, and <nav>.'],
                    ['content' => 'CSS frameworks like Bootstrap and Tailwind CSS simplify styling with predefined classes.'],
                    ['content' => 'Flexbox and CSS Grid allow efficient layout management for web pages.'],
                    ['content' => 'Forms in HTML use <input>, <select>, and <textarea> elements for user interactions.'],
                    ['content' => 'A good understanding of HTML & CSS is essential for front-end development.'],
                ],
                'activity' => [
                    ['title' => 'HTML & CSS Challenge', 'question' => 'Which CSS property controls the space between elements?', 'correct_answer' => 'Margin', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 35, // Web Development -> JavaScript
                'name' => 'JavaScript',
                'data' => [
                    ['content' => 'JavaScript is a scripting language that enables dynamic behavior on web pages.'],
                    ['content' => 'It allows developers to manipulate the DOM (Document Object Model) for interactive elements.'],
                    ['content' => 'JavaScript uses variables like var, let, and const for storing values.'],
                    ['content' => 'Functions in JavaScript can be declared using function keyword or arrow functions (=>).'],
                    ['content' => 'Event listeners allow JavaScript to respond to user actions like clicks and key presses.'],
                    ['content' => 'Promises and async/await enable handling asynchronous operations.'],
                    ['content' => 'JavaScript frameworks like React, Vue, and Angular simplify front-end development.'],
                    ['content' => 'ES6 introduced new features such as template literals, spread/rest operators, and destructuring.'],
                    ['content' => 'JavaScript works with APIs to fetch and send data dynamically using Fetch or Axios.'],
                    ['content' => 'Understanding JavaScript is essential for creating modern, interactive web applications.'],
                ],
                'activity' => [
                    ['title' => 'JavaScript Quiz', 'question' => 'Which keyword is used to declare a constant variable in JavaScript?', 'correct_answer' => 'const', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 36, // Web Development -> Backend Frameworks
                'name' => 'Backend Frameworks',
                'data' => [
                    ['content' => 'Backend frameworks provide tools for building server-side applications efficiently.'],
                    ['content' => 'Popular backend frameworks include Laravel (PHP), Express.js (Node.js), Django (Python), and Spring Boot (Java).'],
                    ['content' => 'Backend frameworks handle database operations, authentication, and API development.'],
                    ['content' => 'Laravel simplifies backend development with built-in features like Eloquent ORM, authentication, and routing.'],
                    ['content' => 'Express.js is a lightweight and flexible Node.js framework for building REST APIs.'],
                    ['content' => 'Django follows the "batteries-included" philosophy, providing built-in tools for web development.'],
                    ['content' => 'Spring Boot streamlines Java-based web applications with minimal configuration.'],
                    ['content' => 'Backend frameworks interact with databases using SQL or NoSQL solutions like MySQL, PostgreSQL, and MongoDB.'],
                    ['content' => 'Middleware functions in backend frameworks handle request processing and security.'],
                    ['content' => 'Choosing the right backend framework depends on project requirements, performance needs, and developer expertise.'],
                ],
                'activity' => [
                    ['title' => 'Backend Frameworks Quiz', 'question' => 'Which backend framework is built on Node.js?', 'correct_answer' => 'Express.js', 'difficulty' => 'expert']
                ]
            ],
            //
            [
                'module_id' => 37, // Mobile Development -> Android Development
                'name' => 'Android Development',
                'data' => [
                    ['content' => 'Android development involves creating applications for the Android operating system using Kotlin or Java.'],
                    ['content' => 'Android Studio is the official IDE for Android development, providing tools for UI design and debugging.'],
                    ['content' => 'The Android SDK includes APIs for accessing device features like GPS, camera, and storage.'],
                    ['content' => 'Activities represent screens in an Android app, managed in the AndroidManifest.xml file.'],
                    ['content' => 'Fragments allow flexible UI design by enabling multiple views in a single activity.'],
                    ['content' => 'Jetpack Compose is the modern UI toolkit for building native Android interfaces with declarative syntax.'],
                    ['content' => 'MVVM (Model-View-ViewModel) is the preferred architecture pattern for Android apps.'],
                    ['content' => 'Firebase provides backend services like authentication, database storage, and push notifications.'],
                    ['content' => 'Publishing an Android app requires signing it with a keystore and uploading it to Google Play.'],
                    ['content' => 'Understanding Android development enables building powerful mobile applications.'],
                ],
                'activity' => [
                    ['title' => 'Android Basics Quiz', 'question' => 'Which language is officially recommended for Android development?', 'correct_answer' => 'Kotlin', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 38, // Mobile Development -> iOS Development
                'name' => 'iOS Development',
                'data' => [
                    ['content' => 'iOS development involves building applications for Apple devices using Swift and Xcode.'],
                    ['content' => 'Swift is the primary programming language for developing iOS applications.'],
                    ['content' => 'Xcode is Apple’s official IDE for iOS development, providing tools for UI design, coding, and debugging.'],
                    ['content' => 'UIKit and SwiftUI are frameworks for designing iOS user interfaces.'],
                    ['content' => 'ViewControllers manage the navigation and user interactions in iOS apps.'],
                    ['content' => 'CoreData enables local data storage and persistence for iOS applications.'],
                    ['content' => 'Apple’s Human Interface Guidelines (HIG) ensure consistency in iOS app design.'],
                    ['content' => 'Testing iOS apps is done using XCTest and Instruments for performance analysis.'],
                    ['content' => 'To publish an iOS app, developers must enroll in the Apple Developer Program.'],
                    ['content' => 'Understanding iOS development allows creating high-quality applications for Apple’s ecosystem.'],
                ],
                'activity' => [
                    ['title' => 'iOS Basics Quiz', 'question' => 'What is the primary programming language used in iOS development?', 'correct_answer' => 'Swift', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 39, // Mobile Development -> Cross-Platform Apps
                'name' => 'Cross-Platform Apps',
                'data' => [
                    ['content' => 'Cross-platform development allows building apps that run on both Android and iOS using a single codebase.'],
                    ['content' => 'Popular cross-platform frameworks include Flutter, React Native, and Xamarin.'],
                    ['content' => 'Flutter uses Dart and provides a rich set of customizable widgets for UI design.'],
                    ['content' => 'React Native enables building mobile apps using JavaScript and React components.'],
                    ['content' => 'Xamarin allows using C# and .NET to develop native-like applications.'],
                    ['content' => 'Cross-platform development reduces code duplication and accelerates development time.'],
                    ['content' => 'Performance optimizations are necessary since cross-platform apps may not be as fast as native apps.'],
                    ['content' => 'Plugins and third-party libraries extend functionality in cross-platform frameworks.'],
                    ['content' => 'Cross-platform tools provide hot reload for faster development iterations.'],
                    ['content' => 'Choosing the right framework depends on project needs, performance considerations, and developer expertise.'],
                ],
                'activity' => [
                    ['title' => 'Cross-Platform Quiz', 'question' => 'Which framework uses Dart as its programming language?', 'correct_answer' => 'Flutter', 'difficulty' => 'expert']
                ]
            ],
            //
            [
                'module_id' => 40, // IT Project Management -> Project Planning
                'name' => 'Project Planning',
                'data' => [
                    ['content' => 'Project planning is the process of defining objectives, scope, deliverables, and timelines before project execution.'],
                    ['content' => 'A well-structured project plan includes tasks, milestones, dependencies, and resource allocation.'],
                    ['content' => 'Defining project goals ensures alignment with business objectives and stakeholder expectations.'],
                    ['content' => 'Work Breakdown Structure (WBS) divides the project into smaller, manageable tasks for better tracking.'],
                    ['content' => 'Gantt charts provide a visual representation of project schedules and deadlines.'],
                    ['content' => 'The Critical Path Method (CPM) helps identify key tasks that determine the project’s overall duration.'],
                    ['content' => 'Project managers use risk assessments to anticipate potential challenges and create contingency plans.'],
                    ['content' => 'Stakeholder communication plans ensure transparency and alignment with project progress.'],
                    ['content' => 'Project planning also includes defining key performance indicators (KPIs) for measuring success.'],
                    ['content' => 'Proper planning reduces risks, enhances efficiency, and increases the likelihood of project success.'],
                ],
                'activity' => [
                    ['title' => 'Project Planning Challenge', 'question' => 'Which tool visually represents project timelines and dependencies?', 'correct_answer' => 'Gantt Chart', 'difficulty' => 'novice']
                ]
            ],
            [
                'module_id' => 41, // IT Project Management -> Risk Management
                'name' => 'Risk Management',
                'data' => [
                    ['content' => 'Risk management in IT projects involves identifying, assessing, and mitigating risks to ensure project success.'],
                    ['content' => 'Common IT project risks include budget overruns, scope creep, and technical failures.'],
                    ['content' => 'A risk assessment matrix classifies risks based on their probability and impact.'],
                    ['content' => 'Mitigation strategies include risk avoidance, reduction, transfer, and acceptance.'],
                    ['content' => 'Contingency plans help prepare teams for unexpected challenges, minimizing project disruption.'],
                    ['content' => 'Regular risk reviews and monitoring allow for proactive management of potential issues.'],
                    ['content' => 'Cybersecurity risks, such as data breaches and system vulnerabilities, require strong security measures.'],
                    ['content' => 'Legal and compliance risks must be considered, especially in data protection and software licensing.'],
                    ['content' => 'Stakeholder engagement is crucial in risk management to address concerns and set realistic expectations.'],
                    ['content' => 'A well-defined risk management plan improves project resilience and reduces overall project uncertainty.'],
                ],
                'activity' => [
                    ['title' => 'Risk Management Quiz', 'question' => 'What is the process of evaluating potential project risks?', 'correct_answer' => 'Risk Assessment', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 42, // IT Project Management -> Scrum Framework
                'name' => 'Scrum Framework',
                'data' => [
                    ['content' => 'Scrum is an agile framework used in software development for iterative and incremental project delivery.'],
                    ['content' => 'Scrum teams consist of a Product Owner, Scrum Master, and Developers working collaboratively.'],
                    ['content' => 'A Scrum project is divided into sprints, which are time-boxed iterations (usually 2-4 weeks long).'],
                    ['content' => 'The Product Backlog contains prioritized features, tasks, and user stories for development.'],
                    ['content' => 'The Sprint Planning Meeting defines what work will be completed in the upcoming sprint.'],
                    ['content' => 'Daily stand-up meetings help teams stay aligned, discuss progress, and address obstacles.'],
                    ['content' => 'Sprint Reviews allow stakeholders to inspect progress and provide feedback on completed work.'],
                    ['content' => 'Sprint Retrospectives help teams reflect on what went well and what can be improved.'],
                    ['content' => 'Scrum emphasizes adaptability, continuous improvement, and delivering value early.'],
                    ['content' => 'Understanding Scrum principles enables teams to manage complex IT projects efficiently.'],
                ],
                'activity' => [
                    ['title' => 'Scrum Framework Quiz', 'question' => 'What is the time-boxed event where teams reflect on their work?', 'correct_answer' => 'Sprint Retrospective', 'difficulty' => 'expert']
                ]
            ],
            //
            [
                'module_id' => 43, // AI -> Machine Learning Basics
                'name' => 'Machine Learning Basics',
                'data' => [
                    ['content' => 'Machine learning (ML) is a subset of AI that enables systems to learn and improve from experience.'],
                    ['content' => 'Supervised learning involves training a model with labeled data to make predictions.'],
                    ['content' => 'Unsupervised learning finds patterns in data without labeled outputs, such as clustering.'],
                    ['content' => 'Reinforcement learning involves an agent interacting with an environment to maximize rewards.'],
                    ['content' => 'Common ML algorithms include decision trees, support vector machines (SVM), and k-nearest neighbors (KNN).'],
                    ['content' => 'Training a machine learning model requires datasets, algorithms, and evaluation metrics.'],
                    ['content' => 'Feature engineering involves selecting and transforming input data to improve model accuracy.'],
                    ['content' => 'Overfitting occurs when a model learns noise instead of patterns, reducing generalization.'],
                    ['content' => 'Deep learning, a subset of ML, utilizes neural networks for complex data processing.'],
                    ['content' => 'Machine learning applications include spam filtering, fraud detection, and recommendation systems.'],
                ],
                'activity' => [
                    ['title' => 'Machine Learning Quiz', 'question' => 'Which type of machine learning does not use labeled data?', 'correct_answer' => 'Unsupervised Learning', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 44, // AI -> Neural Networks
                'name' => 'Neural Networks',
                'data' => [
                    ['content' => 'Neural networks are computational models inspired by the human brain, used for deep learning.'],
                    ['content' => 'A neural network consists of input layers, hidden layers, and an output layer.'],
                    ['content' => 'Each neuron in a neural network applies an activation function to determine its output.'],
                    ['content' => 'Common activation functions include ReLU, Sigmoid, and Softmax.'],
                    ['content' => 'Backpropagation is the process of adjusting weights to minimize errors in training.'],
                    ['content' => 'Convolutional Neural Networks (CNNs) are specialized for image recognition tasks.'],
                    ['content' => 'Recurrent Neural Networks (RNNs) are used for sequential data like speech and text processing.'],
                    ['content' => 'Training deep neural networks requires large datasets and powerful computational resources.'],
                    ['content' => 'Neural networks have applications in natural language processing, facial recognition, and robotics.'],
                    ['content' => 'Understanding neural networks enables developers to build advanced AI-powered applications.'],
                ],
                'activity' => [
                    ['title' => 'Neural Networks Quiz', 'question' => 'What type of neural network is commonly used for image recognition?', 'correct_answer' => 'Convolutional Neural Network (CNN)', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 45, // AI -> AI Ethics
                'name' => 'AI Ethics',
                'data' => [
                    ['content' => 'AI ethics focuses on ensuring that AI systems are fair, transparent, and accountable.'],
                    ['content' => 'Bias in AI occurs when training data reflects social inequalities, leading to unfair decisions.'],
                    ['content' => 'Privacy concerns arise when AI systems collect and analyze vast amounts of personal data.'],
                    ['content' => 'AI accountability ensures that developers and organizations are responsible for AI outcomes.'],
                    ['content' => 'Explainability is the ability to understand and interpret how AI models make decisions.'],
                    ['content' => 'Ethical AI development prioritizes inclusivity, fairness, and non-discriminatory practices.'],
                    ['content' => 'AI in surveillance and autonomous weapons raises significant ethical concerns.'],
                    ['content' => 'Regulations such as GDPR influence how AI can collect and process personal data.'],
                    ['content' => 'AI governance frameworks guide organizations in ethical AI deployment.'],
                    ['content' => 'Ensuring ethical AI practices is essential for building trust in AI-driven technologies.'],
                ],
                'activity' => [
                    ['title' => 'AI Ethics Quiz', 'question' => 'What term refers to the ability to explain AI decision-making?', 'correct_answer' => 'Explainability', 'difficulty' => 'expert']
                ]
            ],
            //
            [
                'module_id' => 46, // Cybersecurity -> Encryption Techniques
                'name' => 'Encryption Techniques',
                'data' => [
                    ['content' => 'Encryption is the process of converting data into a secure format to prevent unauthorized access.'],
                    ['content' => 'Symmetric encryption uses a single key for both encryption and decryption (e.g., AES, DES).'],
                    ['content' => 'Asymmetric encryption uses a pair of keys: a public key for encryption and a private key for decryption (e.g., RSA, ECC).'],
                    ['content' => 'Hashing is a one-way cryptographic process used for verifying data integrity (e.g., SHA-256, MD5).'],
                    ['content' => 'Digital signatures provide authentication and integrity verification using cryptographic hashes and public key infrastructure (PKI).'],
                    ['content' => 'End-to-end encryption (E2EE) ensures that data remains encrypted throughout transmission (e.g., TLS, Signal Protocol).'],
                    ['content' => 'Cryptanalysis is the study of breaking encryption methods and identifying vulnerabilities.'],
                    ['content' => 'Quantum cryptography explores encryption methods that leverage quantum mechanics for security.'],
                    ['content' => 'Key management is critical in encryption to prevent unauthorized access to sensitive data.'],
                    ['content' => 'Strong encryption ensures data confidentiality, integrity, and authenticity in cybersecurity.'],
                ],
                'activity' => [
                    ['title' => 'Encryption Techniques Quiz', 'question' => 'Which encryption method uses a pair of keys for encryption and decryption?', 'correct_answer' => 'Asymmetric Encryption', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 47, // Cybersecurity -> Network Security
                'name' => 'Network Security',
                'data' => [
                    ['content' => 'Network security protects data, devices, and resources from unauthorized access and cyber threats.'],
                    ['content' => 'Firewalls act as a barrier between trusted and untrusted networks to filter traffic.'],
                    ['content' => 'Intrusion Detection Systems (IDS) and Intrusion Prevention Systems (IPS) monitor network activity for malicious behavior.'],
                    ['content' => 'Virtual Private Networks (VPNs) encrypt internet traffic to ensure secure remote access.'],
                    ['content' => 'Secure Socket Layer (SSL) and Transport Layer Security (TLS) encrypt web communications.'],
                    ['content' => 'DDoS (Distributed Denial-of-Service) attacks overwhelm a network to disrupt services.'],
                    ['content' => 'Zero Trust security model ensures that no user or device is trusted by default.'],
                    ['content' => 'Endpoint security solutions protect individual devices from malware and cyber threats.'],
                    ['content' => 'Security patches and updates help protect networks from known vulnerabilities.'],
                    ['content' => 'Network security best practices include strong authentication, least privilege access, and encrypted communication.'],
                ],
                'activity' => [
                    ['title' => 'Network Security Quiz', 'question' => 'What does a firewall do in network security?', 'correct_answer' => 'Filters network traffic', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 48, // Cybersecurity -> Penetration Testing
                'name' => 'Penetration Testing',
                'data' => [
                    ['content' => 'Penetration testing (pentesting) simulates cyberattacks to evaluate security weaknesses.'],
                    ['content' => 'Black-box testing assesses security without prior knowledge of the system.'],
                    ['content' => 'White-box testing provides testers with full access to source code and system architecture.'],
                    ['content' => 'Gray-box testing combines elements of both black-box and white-box approaches.'],
                    ['content' => 'Common pentesting tools include Metasploit, Nmap, Burp Suite, and Wireshark.'],
                    ['content' => 'The OWASP Top 10 highlights the most critical web security risks (e.g., SQL injection, XSS).'],
                    ['content' => 'Social engineering tests assess human vulnerabilities to phishing and manipulation.'],
                    ['content' => 'Physical penetration testing evaluates access control and security of physical locations.'],
                    ['content' => 'Regular penetration testing helps organizations proactively identify and mitigate security risks.'],
                    ['content' => 'Ethical hacking certifications like CEH and OSCP validate cybersecurity expertise.'],
                ],
                'activity' => [
                    ['title' => 'Penetration Testing Quiz', 'question' => 'What is the purpose of penetration testing?', 'correct_answer' => 'To evaluate security vulnerabilities', 'difficulty' => 'advanced']
                ]
            ],
            //
            [
                'module_id' => 49, // Cloud Computing -> AWS & Azure Basics
                'name' => 'AWS & Azure Basics',
                'data' => [
                    ['content' => 'Cloud computing provides on-demand computing resources over the internet.'],
                    ['content' => 'Amazon Web Services (AWS) and Microsoft Azure are two of the leading cloud providers.'],
                    ['content' => 'Infrastructure as a Service (IaaS) offers virtual machines, storage, and networking resources.'],
                    ['content' => 'Platform as a Service (PaaS) provides a managed environment for application development.'],
                    ['content' => 'Software as a Service (SaaS) delivers software applications over the internet (e.g., Gmail, Dropbox).'],
                    ['content' => 'AWS offers services like EC2 for computing, S3 for storage, and RDS for databases.'],
                    ['content' => 'Azure provides cloud solutions such as Azure Virtual Machines, Blob Storage, and Cosmos DB.'],
                    ['content' => 'Cloud security includes identity and access management (IAM) and data encryption.'],
                    ['content' => 'Hybrid cloud strategies combine on-premise infrastructure with cloud services.'],
                    ['content' => 'Cloud adoption reduces costs, increases scalability, and improves resource management.'],
                ],
                'activity' => [
                    ['title' => 'Cloud Computing Quiz', 'question' => 'Which cloud computing model provides managed development environments?', 'correct_answer' => 'Platform as a Service (PaaS)', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 50, // Cloud Computing -> Virtualization
                'name' => 'Virtualization',
                'data' => [
                    ['content' => 'Virtualization allows multiple virtual machines (VMs) to run on a single physical server.'],
                    ['content' => 'A hypervisor (e.g., VMware ESXi, Microsoft Hyper-V) manages virtual machines on a host.'],
                    ['content' => 'Virtualization improves resource utilization, scalability, and disaster recovery.'],
                    ['content' => 'Containerization (e.g., Docker, Kubernetes) provides lightweight, portable software environments.'],
                    ['content' => 'Storage virtualization enables flexible and scalable data storage solutions.'],
                    ['content' => 'Network virtualization allows multiple virtual networks to run independently on a single infrastructure.'],
                    ['content' => 'Desktop virtualization enables remote access to operating systems and applications.'],
                    ['content' => 'Virtual machines can be migrated between servers to optimize performance.'],
                    ['content' => 'Virtualized environments support hybrid and multi-cloud deployments.'],
                    ['content' => 'Understanding virtualization helps businesses optimize IT resources efficiently.'],
                ],
                'activity' => [
                    ['title' => 'Virtualization Quiz', 'question' => 'What is the primary role of a hypervisor?', 'correct_answer' => 'Managing virtual machines', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 51, // Cloud Computing -> Microservices
                'name' => 'Microservices',
                'data' => [
                    ['content' => 'Microservices architecture breaks applications into small, independently deployable services.'],
                    ['content' => 'Each microservice focuses on a specific function and communicates with others via APIs.'],
                    ['content' => 'Microservices improve scalability, flexibility, and fault isolation in applications.'],
                    ['content' => 'Common microservices frameworks include Spring Boot, Django, and Node.js.'],
                    ['content' => 'Containers like Docker and orchestration tools like Kubernetes streamline microservices deployment.'],
                    ['content' => 'Service discovery allows microservices to dynamically locate and communicate with each other.'],
                    ['content' => 'Load balancing ensures even distribution of network traffic among services.'],
                    ['content' => 'Microservices enable continuous integration and deployment (CI/CD) for faster updates.'],
                    ['content' => 'Event-driven microservices use messaging queues (e.g., RabbitMQ, Kafka) for communication.'],
                    ['content' => 'Adopting microservices helps organizations build highly scalable and resilient applications.'],
                ],
                'activity' => [
                    ['title' => 'Microservices Quiz', 'question' => 'What is a key advantage of microservices architecture?', 'correct_answer' => 'Scalability and flexibility', 'difficulty' => 'advanced']
                ]
            ],
            //
            [
                'module_id' => 52, // Capstone Project -> Project Proposal
                'name' => 'Project Proposal',
                'data' => [
                    ['content' => 'A project proposal is a document that outlines the objectives, scope, and significance of the capstone project.'],
                    ['content' => 'The introduction should provide an overview of the problem, the motivation behind the project, and its potential impact.'],
                    ['content' => 'A literature review examines previous studies or existing systems to justify the need for the proposed solution.'],
                    ['content' => 'The project methodology outlines the development approach, tools, technologies, and techniques to be used.'],
                    ['content' => 'A timeline and Gantt chart help visualize the estimated completion period for different project phases.'],
                    ['content' => 'Feasibility analysis determines whether the project is viable based on technical, economic, and operational considerations.'],
                    ['content' => 'A risk assessment identifies potential challenges and provides mitigation strategies.'],
                    ['content' => 'Expected outcomes should clearly define what the project aims to achieve, including its deliverables and contributions.'],
                    ['content' => 'A budget plan outlines estimated costs, including software, hardware, and development expenses.'],
                    ['content' => 'Project proposals must be reviewed and approved before proceeding to the development phase.'],
                ],
                'activity' => [
                    ['title' => 'Project Proposal Review', 'question' => 'What section of the project proposal explains the tools and technologies used?', 'correct_answer' => 'Methodology', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 53, // Capstone Project -> Development Phases
                'name' => 'Development Phases',
                'data' => [
                    ['content' => 'Capstone projects typically follow a structured development process to ensure successful implementation.'],
                    ['content' => 'The planning phase involves defining project requirements, finalizing the proposal, and creating a development roadmap.'],
                    ['content' => 'Design and prototyping involve creating wireframes, UI/UX designs, and database schemas.'],
                    ['content' => 'Implementation includes coding the application, integrating databases, and ensuring functionality according to requirements.'],
                    ['content' => 'Testing ensures the system is free from bugs, using techniques like unit testing, integration testing, and user acceptance testing (UAT).'],
                    ['content' => 'Documentation includes writing user manuals, system architecture descriptions, and developer guides.'],
                    ['content' => 'Deployment involves setting up the system on a server or distributing the application to users.'],
                    ['content' => 'Evaluation measures the success of the project by comparing expected outcomes with actual results.'],
                    ['content' => 'Maintenance and updates ensure the system remains functional and efficient after deployment.'],
                    ['content' => 'Capstone projects may require revisions based on feedback from mentors or panel evaluations.'],
                ],
                'activity' => [
                    ['title' => 'Development Stages Quiz', 'question' => 'What testing phase ensures that the system meets user expectations?', 'correct_answer' => 'User Acceptance Testing (UAT)', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 54, // Capstone Project -> Final Presentation
                'name' => 'Final Presentation',
                'data' => [
                    ['content' => 'The final presentation is an opportunity to showcase the completed capstone project to a panel of evaluators.'],
                    ['content' => 'A well-structured presentation includes an introduction, system demonstration, results, and conclusion.'],
                    ['content' => 'Visual aids like PowerPoint slides, charts, and system screenshots enhance understanding.'],
                    ['content' => 'Live demonstrations allow the panel to assess the functionality and usability of the system.'],
                    ['content' => 'Highlighting key features, unique aspects, and benefits helps make the project stand out.'],
                    ['content' => 'Anticipating and preparing for questions from the panel demonstrates a strong grasp of the project.'],
                    ['content' => 'Proper time management ensures that all essential points are covered within the allotted duration.'],
                    ['content' => 'Professionalism in communication, body language, and attire leaves a positive impression.'],
                    ['content' => 'Providing a comprehensive project report supports the presentation with detailed explanations.'],
                    ['content' => 'Receiving feedback from the panel helps identify areas for improvement and future development.'],
                ],
                'activity' => [
                    ['title' => 'Presentation Assessment', 'question' => 'What is the purpose of a live demonstration during the final presentation?', 'correct_answer' => 'To showcase system functionality', 'difficulty' => 'advanced']
                ]
            ],
            //
            [
                'module_id' => 55, // Ethics in IT -> Legal Aspects
                'name' => 'Legal Aspects',
                'data' => [
                    ['content' => 'Ethics in IT involves legal considerations that protect users, organizations, and intellectual property.'],
                    ['content' => 'Intellectual property laws govern copyrights, trademarks, and patents in software and technology.'],
                    ['content' => 'The Computer Fraud and Abuse Act (CFAA) penalizes unauthorized access to computer systems.'],
                    ['content' => 'Cybercrime laws address hacking, identity theft, and online fraud.'],
                    ['content' => 'The Digital Millennium Copyright Act (DMCA) protects against illegal software distribution and piracy.'],
                    ['content' => 'GDPR (General Data Protection Regulation) regulates data privacy and security in the European Union.'],
                    ['content' => 'Software licensing agreements define terms for legal use, distribution, and modification of software.'],
                    ['content' => 'Net neutrality ensures equal access to online content without discrimination.'],
                    ['content' => 'Cybersecurity regulations mandate security measures to protect sensitive data.'],
                    ['content' => 'Understanding IT laws helps professionals avoid legal risks and ethical violations.'],
                ],
                'activity' => [
                    ['title' => 'Legal Ethics Quiz', 'question' => 'What law penalizes unauthorized access to computer systems?', 'correct_answer' => 'Computer Fraud and Abuse Act (CFAA)', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 56, // Ethics in IT -> Professional Conduct
                'name' => 'Professional Conduct',
                'data' => [
                    ['content' => 'Professional ethics in IT ensure responsible and ethical behavior among technology professionals.'],
                    ['content' => 'The ACM Code of Ethics outlines principles for ethical decision-making in computing.'],
                    ['content' => 'Honesty and integrity require IT professionals to be truthful in their work and communications.'],
                    ['content' => 'Respect for privacy ensures user data is handled securely and with consent.'],
                    ['content' => 'Conflict of interest policies prevent IT professionals from engaging in unethical business practices.'],
                    ['content' => 'Ensuring accessibility in IT solutions promotes inclusivity and fairness.'],
                    ['content' => 'Responsible use of AI and automation prevents bias and ensures ethical outcomes.'],
                    ['content' => 'Whistleblowing policies protect employees who report unethical practices.'],
                    ['content' => 'Employers and employees should engage in continuous ethical training.'],
                    ['content' => 'Ethical IT practices help maintain trust, credibility, and professionalism.'],
                ],
                'activity' => [
                    ['title' => 'Ethical Conduct Quiz', 'question' => 'Which ethical code provides guidelines for responsible computing?', 'correct_answer' => 'ACM Code of Ethics', 'difficulty' => 'expert']
                ]
            ],
            [
                'module_id' => 57, // Ethics in IT -> Data Privacy Laws
                'name' => 'Data Privacy Laws',
                'data' => [
                    ['content' => 'Data privacy laws regulate how organizations collect, store, and share personal data.'],
                    ['content' => 'The Data Privacy Act protects individuals’ personal data from misuse.'],
                    ['content' => 'The GDPR enforces strict rules on data protection within the European Union.'],
                    ['content' => 'The California Consumer Privacy Act (CCPA) gives users rights over their personal data.'],
                    ['content' => 'Data breach notification laws require organizations to inform users of security incidents.'],
                    ['content' => 'Companies must obtain user consent before collecting personal data.'],
                    ['content' => 'Strong encryption and cybersecurity measures enhance data protection.'],
                    ['content' => 'Data minimization ensures that only necessary information is collected.'],
                    ['content' => 'Privacy policies should be transparent and easily accessible to users.'],
                    ['content' => 'Non-compliance with privacy laws can lead to severe penalties and legal actions.'],
                ],
                'activity' => [
                    ['title' => 'Data Privacy Quiz', 'question' => 'Which law regulates data privacy in the European Union?', 'correct_answer' => 'GDPR', 'difficulty' => 'advanced']
                ]
            ],
        ];

        foreach ($lessons as &$lesson) {
            //update year_id and subjects
            $subjectModule = DB::table('subject_modules')->where('name', $lesson['name'])->first();
            $subject = DB::table('subjects')->where('id', $subjectModule->subject_id)->first();

            $lesson['data'] = json_encode($lesson['data']);
            $lesson['activity'] = json_encode($lesson['activity']);
            $lesson['created_at'] = now();
            $lesson['updated_at'] = now();

            $lesson['subject_id'] = $subjectModule->subject_id;
            $lesson['year_id'] = $subject->year_id;
        }

        DB::table('module_lessons')->insert($lessons);
    }
}
