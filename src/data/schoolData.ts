import { ContinuumStage, StatItem, SuccessStory, CampusFacility, AdmissionStep, NewsEvent } from '../types';

export const CONTINUUM_STAGES: ContinuumStage[] = [
  {
    id: 'early-years',
    stageNumber: '01',
    name: 'Early Years Foundation',
    subtitle: 'Discovery & Play-Based Inception',
    ageRange: 'Ages 2.5 – 5 Years',
    description: 'An inquiry-led exploratory sanctuary where early learners discover wonder, bilingual vocabulary, emotional intelligence, and cognitive curiosity through sensory experiences and purposeful play.',
    pillars: [
      'Bilingual Language Immersion (English & Native)',
      'Montessori & Reggio Emilia Hybrid Approach',
      'Sensory & Nature Exploration Gardens',
      'Foundational Phonics, Motor Skills & Numeracy'
    ],
    accreditationBadge: 'Cambridge Early Years Accredited',
    accreditationDetail: 'Nurturing joyful, independent, and empathetic thinkers from their earliest steps.',
    image: '/assets/images/early-years.jpg'
  },
  {
    id: 'primary-continuum',
    stageNumber: '02',
    name: 'Primary Continuum',
    subtitle: 'Project-Based Learning & Inquiry',
    ageRange: 'Grades 1 – 5 · Ages 5 – 10 Years',
    description: 'Inspired by world-leading inquiry frameworks and CSI approaches, our primary school fosters conceptual understanding. Scholars connect mathematics, environmental sciences, languages, and the creative arts through authentic real-world challenges.',
    pillars: [
      'Project-Based Learning (PBL) Framework',
      'Singapore Mathematical Logic & Problem-Solving',
      'Junior Coding & Visual Robotics Integration',
      'Global Perspectives & Cultural Competence'
    ],
    accreditationBadge: 'Cambridge Primary & Global Standards',
    accreditationDetail: 'Cultivating critical inquiry and scientific reasoning through interactive discovery.',
    image: '/assets/images/primary-years.jpg'
  },
  {
    id: 'middle-years',
    stageNumber: '03',
    name: 'Middle Years Academy',
    subtitle: 'Critical Inquiry & STEM Innovation',
    ageRange: 'Grades 6 – 8 · Ages 11 – 13 Years',
    description: 'Bridging foundational literacy with interdisciplinary rigour. Learners conduct rigorous laboratory experiments, master computational thinking, participate in Model United Nations, and lead community service initiatives.',
    pillars: [
      'Advanced STEM, 3D Prototyping & AI Literacy',
      'Model United Nations & Rhetorical Debating',
      'Integrated Experimental Sciences (Bio, Chem, Phys)',
      'Ethical Leadership & Digital Citizenship'
    ],
    accreditationBadge: 'Cambridge Lower Secondary Center',
    accreditationDetail: 'Empowering adolescent scholars with analytical acumen and self-directed agency.',
    image: '/assets/images/middle-years.jpg'
  },
  {
    id: 'senior-cambridge',
    stageNumber: '04',
    name: 'Senior Cambridge & IGCSE',
    subtitle: 'Academic Rigour & International Credentials',
    ageRange: 'Grades 9 – 12 · Ages 14 – 18 Years',
    description: 'Widely recognized as the gold standard of secondary education, our senior programmes offer deep subject mastery, university-level research colloquiums, and personalized portfolio development under certified international faculty.',
    pillars: [
      'Cambridge IGCSE, O Levels & A Levels Tracks',
      'Independent Research Colloquiums & EPQ',
      'Pre-University Career Mentorship & Aptitude',
      'Dual Honors & Accelerated Subject Pathways'
    ],
    accreditationBadge: 'Cambridge International Fellow School',
    accreditationDetail: 'Consistently producing Regional & World distinction winners across Cambridge exams.',
    image: '/assets/images/senior-cambridge.jpg'
  },
  {
    id: 'flight-path',
    stageNumber: '05',
    name: 'Beyond GenZ: Global Flight Path',
    subtitle: 'University Launch & Career Flight',
    ageRange: 'Post-Secondary & Higher Education Pathways',
    description: 'Embodying the soaring airplane motif of our brand, our in-house Global University Advisory guides scholars directly to elite institutions worldwide with strategic SAT/IELTS preparation and full-tuition scholarship packaging.',
    pillars: [
      'Ivy League & Russell Group Admissions Strategy',
      'Comprehensive SAT / ACT / IELTS In-House Training',
      'Merit Scholarship & Financial Aid Strategy',
      'Global Alumni Mentorship Network in 14 Nations'
    ],
    accreditationBadge: 'Global University Admissions Guarantee',
    accreditationDetail: 'Over $12M in global merit scholarships and fellowships secured by graduating cohorts.',
    image: '/assets/images/beyond-pathway.jpg'
  }
];

export const KEY_STATS: StatItem[] = [
  {
    id: 'ratio',
    number: 8,
    isRatio: true,
    label: 'Student-Teacher Ratio',
    description: 'Guarantees individualized mentorship and responsive care for every scholar.'
  },
  {
    id: 'faculty',
    number: 100,
    suffix: '%',
    label: 'Certified International Faculty',
    description: 'Cambridge-trained educators bringing world-class pedagogy into every classroom.'
  },
  {
    id: 'destinations',
    number: 42,
    suffix: '+',
    label: 'Global University Destinations',
    description: 'Scholars placed across the United States, United Kingdom, Canada, and Europe.'
  },
  {
    id: 'scholarships',
    number: 12,
    prefix: '$',
    suffix: 'M+',
    label: 'Scholarships Awarded',
    description: 'Total merit and fellowship awards earned by GenZ cohorts over the last 5 years.'
  }
];

export const SUCCESS_STORIES: SuccessStory[] = [
  {
    id: 'alizeh',
    studentName: 'Alizeh Tariq',
    batch: "Cambridge A-Level Distinction '24",
    university: 'Stanford University',
    country: 'United States',
    program: 'B.S. Artificial Intelligence & Cognitive Science',
    scholarshipTier: '100% Full Fellowship ($320k)',
    avatar: '/assets/images/student-1.jpg',
    quote: 'The robotics maker space and personal counseling at GenZ gave me the analytical confidence to compete and win on the global stage.'
  },
  {
    id: 'rayan',
    studentName: 'Rayan Malik',
    batch: "Cambridge Scholar '24",
    university: 'University of Oxford',
    country: 'United Kingdom',
    program: 'B.A. Philosophy, Politics & Economics (PPE)',
    scholarshipTier: 'Rhodes Pathway Scholar',
    avatar: '/assets/images/student-2.jpg',
    quote: "GenZ's seminar culture and Model UN delegations prepared me impeccably for Oxford's intense tutorial interview dialogues."
  },
  {
    id: 'sarah',
    studentName: 'Sarah Khan',
    batch: "Senior Scholar '25",
    university: 'University of Toronto',
    country: 'Canada',
    program: 'B.Eng. Biomedical & Neural Engineering',
    scholarshipTier: 'Lester B. Pearson Scholar ($100k)',
    avatar: '/assets/images/student-3.jpg',
    quote: 'The project-based curriculum challenged us to create real medical prototypes instead of simply memorizing textbooks.'
  },
  {
    id: 'daniyal',
    studentName: 'Daniyal Ahmed',
    batch: "Cambridge Scholar '25",
    university: 'Imperial College London',
    country: 'United Kingdom',
    program: 'M.Eng. Aeronautical & Aerospace Systems',
    scholarshipTier: "President's Excellence Award",
    avatar: '/assets/images/student-4.jpg',
    quote: 'From wind tunnel simulation projects to international physics olympiads, GenZ inspired me to reach for the skies.'
  }
];

export const CAMPUS_FACILITIES: CampusFacility[] = [
  {
    id: 'library',
    title: 'Digital Research Library & Media Commons',
    category: 'Academic Exploration',
    description: 'Over 25,000 digital & physical volumes, JSTOR access, private acoustic study pods, and collaborative ideation lounges.',
    image: '/assets/images/campus-library.jpg',
    features: ['JSTOR & ResearchGate Academic Gateways', 'Silent Acoustic Research Pods', 'Collaborative Seminar Suites']
  },
  {
    id: 'stem',
    title: 'Robotics, AI & 3D Prototyping Laboratory',
    category: 'Future Technology',
    description: 'Equipped with autonomous drone test cages, VEX robotics kits, laser cutters, 3D additive printers, and high-performance computing rigs.',
    image: '/assets/images/campus-stem.jpg',
    features: ['VEX & FIRST Robotics Rigs', '3D Rapid Prototyping Stations', 'AI & Machine Learning GPU Workstations']
  },
  {
    id: 'athletics',
    title: 'Athletic Center & Heated Aquatics Complex',
    category: 'Physical Well-being',
    description: 'Semi-Olympic temperature-controlled indoor swimming pool, FIFA-standard all-weather turf, and multi-sport indoor gymnasium.',
    image: '/assets/images/campus-sports.jpg',
    features: ['Olympic Swimming Academy', 'FIFA-Standard Turf Field', 'Dedicated Strength & Conditioning Suite']
  },
  {
    id: 'arts',
    title: 'Performing Arts Theater & Fine Art Atelier',
    category: 'Creative Expression',
    description: 'A 500-seat state-of-the-art acoustic auditorium, black-box drama workshop, and sunlit fine arts painting and pottery studios.',
    image: '/assets/images/campus-arts.jpg',
    features: ['500-Seat Acoustic Auditorium', 'Black-Box Theater Studio', 'Fine Arts & Ceramics Atelier']
  }
];

export const ADMISSION_STEPS: AdmissionStep[] = [
  {
    step: '01',
    title: 'Initial Discovery & Counseling',
    subtitle: 'Connecting Passions with Opportunities',
    description: 'Schedule an introductory dialogue with our admissions deans. We review academic history, explore individual aspirations, and outline tailored curriculum options across our Cambridge continuum.',
    timeEstimate: '30 Mins · In-Person or Virtual',
    actionText: 'Book Discovery Session',
    checklist: [
      'Comprehensive curriculum pathway consultation',
      'Personalized review of past academic transcripts',
      'Q&A regarding scholarship tiers & student life'
    ]
  },
  {
    step: '02',
    title: 'Campus Tour & Diagnostic Inquiry',
    subtitle: 'Experiencing the Learning Environment',
    description: 'Experience our innovation labs, sports complex, and interactive classrooms. Prospective scholars participate in a supportive, age-appropriate diagnostic assessment celebrating logic, language, and creativity.',
    timeEstimate: '1.5 Hours · On-Campus Walkthrough',
    actionText: 'Schedule Private Campus Tour',
    checklist: [
      'Student-guided walkthrough of academic faculties',
      'Child diagnostic assessment in a low-stress setting',
      'Interactive interaction with subject department heads'
    ]
  },
  {
    step: '03',
    title: 'Leadership & Family Dialogue',
    subtitle: 'Shared Values & Long-term Vision',
    description: 'A warm, collaborative interview between parents, scholar, and school leadership to ensure educational aspirations, ethical values, and mutual commitments harmonize for sustained growth.',
    timeEstimate: '45 Mins · Senior Leadership Suite',
    actionText: 'Confirm Interview Slot',
    checklist: [
      'Deep dive into the scholar’s extracurricular passions',
      'Co-creation of individual learning targets',
      'Review of potential global university trajectory'
    ]
  },
  {
    step: '04',
    title: 'Enrolment & Global Flight Pack',
    subtitle: 'Welcoming You to the GenZ Community',
    description: 'Upon acceptance, families receive our comprehensive Scholar Onboarding Flight Pack, complete with portal access credentials, uniform fittings, house assignment, and academic calendar.',
    timeEstimate: 'Instant Confirmation Upon Submission',
    actionText: 'Access Scholar Portal',
    checklist: [
      'Issuance of formal Certificate of Admission',
      'Access credentials for Parent & Scholar 24/7 Portal',
      'Orientation schedule, uniform fittings & house allocation'
    ]
  }
];

export const NEWS_EVENTS: NewsEvent[] = [
  {
    id: 'summit-2026',
    title: 'Annual Global Education & Ivy League Summit 2026',
    date: 'October 28, 2026',
    location: 'Main Auditorium, Islamabad Campus',
    category: 'Global Colloquium',
    excerpt: 'Admissions directors from 35+ top world universities assemble at GenZ for exclusive portfolio reviews, masterclasses, and direct scholarship assessments.',
    image: '/assets/images/event-1.jpg'
  },
  {
    id: 'stem-championship',
    title: 'National Youth STEM & Autonomous Robotics Championship',
    date: 'November 14, 2026',
    location: 'Innovation Quad, Lahore Campus',
    category: 'STEM & Innovation',
    excerpt: 'Middle and Senior Cambridge scholars showcase autonomous drone prototypes, renewable energy solutions, and applied machine-learning solutions.',
    image: '/assets/images/event-2.jpg'
  },
  {
    id: 'genz-mun',
    title: 'GenZMUN VII: Empowering Future Multilateral Diplomats',
    date: 'December 05, 2026',
    location: 'Diplomatic Center, Islamabad Campus',
    category: 'Leadership & Diplomacy',
    excerpt: 'Over 400 student delegates deliberate on global sustainable development goals, digital human rights, and peaceful multilateral treaties.',
    image: '/assets/images/event-3.jpg'
  }
];
