import { getContent, siteConfig } from '../content';
import { ContinuumStage, StatItem, SuccessStory, CampusFacility, AdmissionStep, NewsEvent } from '../types';

export const CONTINUUM_STAGES: ContinuumStage[] = getContent<ContinuumStage[]>("sections.hero.stages", [
  {
    id: 'uk-pathway',
    stageNumber: '01',
    name: 'Study in the United Kingdom',
    subtitle: 'Russell Group Admissions & 2-Year Post-Study Work Permit',
    ageRange: 'Undergraduate & Postgraduate · Jan & Sep 2026',
    description: 'Direct access to 130+ world-renowned UK universities with guaranteed CAS processing. Benefit from £2,000–£10,000 automatic merit scholarships, flexible IELTS waivers, and 2 years post-study work authorization.',
    pillars: [
      '130+ Partner UK Campuses & Russell Group',
      'IELTS Waiver Options for Matric/Inter/A-Levels',
      'Up to £10,000 Merit & Vice-Chancellor Awards',
      '2-Year Graduate Route Work Visa in the UK'
    ],
    accreditationBadge: 'British Council & UCAS Certified',
    accreditationDetail: 'Official registered British Council agency with 99.4% Tier-4 student visa issuance rate.',
    image: '/assets/images/destination-img-1.png'
  },
  {
    id: 'usa-pathway',
    stageNumber: '02',
    name: 'Study in the United States',
    subtitle: 'Tier-1 Campuses, Ivy League & 3-Year STEM OPT Work Rights',
    ageRange: 'STEM Programs, Business & AI · Fall & Spring',
    description: 'Explore top-ranked USA universities with official I-20 issuance, in-state tuition fee grants, and professional 1-on-1 F-1 visa embassy interview coaching by former visa scholars.',
    pillars: [
      'Up to 100% Merit Scholarships & In-State Waivers',
      '3-Year STEM OPT Employment Authorization',
      'Rigorous F-1 Embassy Mock Interview Simulation',
      'SAT / GRE Waivers & Fast-Track I-20 Approval'
    ],
    accreditationBadge: 'US Embassy & AIRC Compliant',
    accreditationDetail: 'Over $12M+ in merit fellowships and tuition grants secured for Pakistani students.',
    image: '/assets/images/hero-campus.jpg'
  },
  {
    id: 'aus-canada-pathway',
    stageNumber: '03',
    name: 'Study in Australia & Canada',
    subtitle: 'Group of Eight & Top Canadian Designated Learning Institutions (DLI)',
    ageRange: 'Bachelors & Masters · 3–5 Year Post-Study Work',
    description: 'Launch your international academic career in Australia and Canada. Full GTE/GST compliance verification, high-ranking DLI placements, and straightforward pathways to PR.',
    pillars: [
      'Streamlined Visa Processing & High GTE Approval',
      'Up to 3–5 Years Post-Study Work Permit (PGWP)',
      'Part-Time Work Rights (48 Hours Per Fortnight)',
      'Regional Campus Incentives & Tuition Reductions'
    ],
    accreditationBadge: 'ICEF & QEAC Certified Agency',
    accreditationDetail: 'Direct institutional partnerships with Group of Eight and leading Canadian universities.',
    image: '/assets/images/destination-img-2.png'
  },
  {
    id: 'europe-germany-pathway',
    stageNumber: '04',
    name: 'Study in Germany & Europe',
    subtitle: 'Zero/Low Tuition Universities & 27 Schengen Countries Travel',
    ageRange: 'Engineering, Tech & Business · 100% English Taught',
    description: 'Pursue tuition-free or ultra-low-cost degrees in Germany, Ireland, Hungary, and Sweden. Comprehensive APS certificate assistance, German blocked account guidance, and European work rights.',
    pillars: [
      '100% Tuition Fee Exemption in German Public Unis',
      'Fully English-Taught Bachelor’s & Master’s Degrees',
      'Freedom of Travel Across 27 Schengen Countries',
      '18-Month Post-Study Job Seeking Visa in Germany'
    ],
    accreditationBadge: 'DAAD & European Higher Education Hub',
    accreditationDetail: 'Complete end-to-end blocked account and APS documentation assistance.',
    image: '/assets/images/campus-building.jpg'
  },
  {
    id: 'visa-ielts-advisory',
    stageNumber: '05',
    name: 'Fast-Track Visa Filing & Test Prep',
    subtitle: '100% Legal Document Curation & Certified IELTS / PTE Coaching',
    ageRange: 'End-to-End Handholding · Profile Assessment to Flight',
    description: 'Our elite team of immigration advocates and certified British Council examiners handles your SOP writing, financial sponsor audits, mock interviews, and pre-departure settlement.',
    pillars: [
      'Official British Council Certified IELTS/PTE Trainers',
      'Impeccable Statement of Purpose (SOP) Curation',
      'Comprehensive Bank Statement & Financial Auditing',
      'Airport Reception, Accommodation & Flight Booking'
    ],
    accreditationBadge: '98.6% Visa Success Ratio',
    accreditationDetail: 'Trusted by 10,000+ Pakistani scholars placed across 850+ global universities worldwide.',
    image: '/assets/images/why-choose-us.png'
  }
]);

export const KEY_STATS: StatItem[] = getContent<StatItem[]>("sections.stats.items", [
  {
    id: 'ratio',
    number: 98.6,
    suffix: '%',
    label: 'Visa Success Ratio',
    description: 'Flawless document verification and embassy interview clearance rate.'
  },
  {
    id: 'faculty',
    number: 850,
    suffix: '+',
    label: 'Partner Universities',
    description: 'Direct tie-ups across the UK, USA, Australia, Canada, and Europe.'
  },
  {
    id: 'destinations',
    number: 25,
    suffix: '+',
    label: 'Global Study Destinations',
    description: 'Placing students across premier international education destinations.'
  },
  {
    id: 'scholarships',
    number: 12,
    prefix: '$',
    suffix: 'M+',
    label: 'Scholarships Secured',
    description: 'Total merit awards and need-based fee discounts won for Pakistani students.'
  }
]);

export const SUCCESS_STORIES: SuccessStory[] = getContent('sections.stories.source', 'backend') === 'backend' ? (siteConfig.catalog.testimonials || []) : getContent<SuccessStory[]>("sections.stories.items", [
  {
    id: 'alizeh',
    studentName: 'Alizeh Tariq',
    batch: "Oxford University Cohort '25",
    university: 'University of Oxford',
    country: 'United Kingdom',
    program: 'M.Sc. Advanced Computer Science',
    scholarshipTier: '100% Full Fellowship (£42k)',
    avatar: '/assets/images/student-1.jpg',
    quote: 'GenZ guided me through every step of my Oxford application, CAS issuance, and Tier-4 visa filing with total transparency.'
  },
  {
    id: 'rayan',
    studentName: 'Rayan Malik',
    batch: "Monash University '25",
    university: 'Monash University',
    country: 'Australia',
    program: 'Master of Data Science',
    scholarshipTier: 'Dean’s International Excellence Award',
    avatar: '/assets/images/student-2.jpg',
    quote: "From GTE documentation to visa grant in just 14 days, GenZ's Australia desk is unmatched in Pakistan."
  },
  {
    id: 'sarah',
    studentName: 'Sarah Khan',
    batch: "University of Toronto '25",
    university: 'University of Toronto',
    country: 'Canada',
    program: 'B.Sc. Biotechnology',
    scholarshipTier: 'International Scholar Award ($25,000)',
    avatar: '/assets/images/student-3.jpg',
    quote: 'They made my dream of studying in Canada possible with zero stress. Their visa filing team is meticulous and honest.'
  }
]);

export const CAMPUS_FACILITIES: CampusFacility[] = getContent<CampusFacility[]>("sections.facilities.items", [
  {
    id: 'counseling-suites',
    title: 'Executive Counseling Suites',
    category: '1-on-1 Advisory',
    description: 'Private, technology-enabled consultation rooms designed for personalized academic profiling and parent conferences.',
    image: '/assets/images/facility-library.jpg',
    features: ['Direct University Video Feeds', 'Profile Evaluation Tech', 'Private Document Auditing']
  },
  {
    id: 'ielts-lab',
    title: 'IELTS / PTE Digital Testing Lab',
    category: 'Language Mastery',
    description: 'Modern language training facility equipped with authentic Cambridge audio systems and PTE test simulation engines.',
    image: '/assets/images/facility-science.jpg',
    features: ['Real-Time Mock Scoring', 'Certified Native Trainers', 'Speaking Practice Pods']
  },
  {
    id: 'visa-center',
    title: 'Diplomatic Visa Processing Center',
    category: 'Immigration Bureau',
    description: 'Dedicated document digitization and embassy biometric coordination bureau ensuring 100% compliance with foreign embassies.',
    image: '/assets/images/facility-stem.jpg',
    features: ['Biometric Scanning Support', 'Financial Document Vault', 'Interview Mock Stage']
  }
]);

export const ADMISSION_STEPS: AdmissionStep[] = getContent<AdmissionStep[]>("sections.admissions.steps", [
  {
    step: '01',
    title: 'Free Profile Assessment',
    subtitle: 'Academic & Career Roadmapping',
    description: 'Bring your academic transcripts, transcripts, and passport for an instant eligibility evaluation by certified counselors.',
    timeEstimate: '30 - 45 Minutes'
  },
  {
    step: '02',
    title: 'University Shortlisting & Offer',
    subtitle: 'Course & Fee Matching',
    description: 'We align your career goals with top global campuses and apply for fast-track conditional offer letters within 48-72 hours.',
    timeEstimate: '2 - 5 Business Days'
  },
  {
    step: '03',
    title: 'Scholarship & CAS / I-20 Issuance',
    subtitle: 'Funding & Confirmation',
    description: 'Our advisors submit fee scholarship applications and coordinate directly with the university for CAS or I-20 issuance.',
    timeEstimate: '1 - 2 Weeks'
  },
  {
    step: '04',
    title: 'Visa Lodgement & Fly Abroad',
    subtitle: 'Embassy Submission & Pre-Departure',
    description: 'Flawless visa file curation, financial sponsorship verification, embassy interview drill, and pre-departure briefing.',
    timeEstimate: '2 - 4 Weeks'
  }
]);

export const NEWS_EVENTS: NewsEvent[] = getContent<NewsEvent[]>("sections.news.items", [
  {
    id: 'expo-2026',
    title: 'GenZ Study Abroad Grand Expo 2026',
    category: 'Flagship Event',
    date: 'Nov 17 - 26, 2026',
    excerpt: 'Meet delegates directly from 100+ UK, USA, Australia, and Canadian universities with spot admissions and scholarships.',
    image: '/assets/images/event-detail-1.png',
    location: 'Serena Hotels & PC Hotels Nationwide'
  },
  {
    id: 'ielts-masterclass',
    title: 'British Council IELTS 8.0+ Band Masterclass',
    category: 'Workshop',
    date: 'Dec 05, 2026',
    excerpt: 'Intensive 2-day workshop led by certified British Council examiners covering high-scoring writing and speaking techniques.',
    image: '/assets/images/course-image-1.png',
    location: 'GenZ Central Auditorium, Lahore'
  },
  {
    id: 'uk-visa-seminar',
    title: 'UK Graduate Route & Tier 4 Visa Curation Seminar',
    category: 'Advisory Session',
    date: 'Dec 18, 2026',
    excerpt: 'Step-by-step guidance on CAS compliance, bank statement requirements, and 2-year post-study work authorization.',
    image: '/assets/images/blog-details-1.png',
    location: 'Online Webinar & Regional Branches'
  }
]);
