import { LeadershipProfile } from '../types';

export const LEADERSHIP_PROFILES: LeadershipProfile[] = [
  {
    id: 'dr-zahid-bajwa',
    name: 'Dr. Zahid Mahmood Bajwa',
    title: 'Director',
    roleBadge: 'Global Academic Directorate & Scholarship Chair',
    nationality: 'Denmark',
    countryCode: 'DK',
    countryFlagEmoji: '🇩🇰',
    image: '/assets/images/team/zahid-bajwa.jpg',
    featuredBadge: 'PhD Aarhus · Erasmus Mundus · UAF Gold Medalist',
    quote: 'Empowering ambitious scholars with world-class mentorship to secure prestigious global fellowships and doctoral opportunities across Scandinavia and Europe.',
    qualifications: [
      {
        degree: 'PhD',
        field: 'Doctoral Research',
        institution: 'Aarhus University',
        country: 'Denmark',
        highlight: true
      },
      {
        degree: 'M.Sc',
        field: 'Erasmus Mundus Joint Master',
        institution: 'Bangor University (United Kingdom) & University of Göttingen (Germany)',
        country: 'UK / Germany',
        highlight: true
      },
      {
        degree: 'B.Sc (Hons)',
        field: 'Academic Gold Medalist',
        institution: 'University of Agriculture Faisalabad (UAF)',
        country: 'Pakistan',
        highlight: true
      }
    ],
    experience: [
      '15+ years of distinguished international consultancy facilitating prestigious global educational scholarships and European research fellowships.',
      'Specialist mentor for Erasmus Mundus Joint Master Degrees (EMJMD), Marie Skłodowska-Curie actions, and Danish government research grants.',
      'Guiding students into top-ranking European universities with 100% full-tuition waivers and living stipends.',
      'Architect of academic articulation protocols ensuring GenZ scholars meet the highest international university matriculation standards.'
    ],
    specializations: [
      'Erasmus Mundus Scholarships',
      'Nordic Higher Education Admissions',
      'PhD & Research Grant Proposals',
      'Curriculum Rigour & Academic Articulation'
    ],
    achievements: [
      'Awarded Academic Gold Medal at UAF for highest honors',
      'Dual Erasmus Mundus Joint Master Fellowship recipient',
      '500+ scholars placed into elite European scholarship programs'
    ]
  },
  {
    id: 'muzaffar-hussain',
    name: 'Muzaffar Hussain',
    title: 'Director',
    roleBadge: 'European Directorate & Enterprise Development',
    nationality: 'Denmark',
    countryCode: 'DK',
    countryFlagEmoji: '🇩🇰',
    image: '/assets/images/team/muzaffar-hussain.jpg',
    featuredBadge: 'Erasmus Mundus Master · Study Abroad Mentor Since 2009',
    quote: 'Combining premier European education credentials with scaled business ventures to open boundless career and residency pathways for our scholars.',
    qualifications: [
      {
        degree: 'M.Sc',
        field: 'Erasmus Mundus Joint Master',
        institution: 'University of Copenhagen (Denmark) & Swedish University of Agricultural Sciences (SLU Sweden)',
        country: 'Denmark / Sweden',
        highlight: true
      }
    ],
    experience: [
      'Europe Study Abroad Mentor since 2009, successfully advising over a decade of scholars across Denmark, Sweden, Germany, and the EU.',
      'International business development strategist and mentor for global franchise expansions including Burger King European territory operations.',
      'Prominent Danish entrepreneur and Founder of Döner King in Denmark (www.donerking.dk), managing scaled multi-location retail operations.',
      'Expert strategist on post-study European work permits, Permanent Residency (PR), and European corporate career launches.'
    ],
    specializations: [
      'Europe Study Abroad Mentorship (Denmark & Nordics)',
      'Multinational Business Franchise Strategy',
      'European Post-Study Work & Settlement Routes',
      'Cross-Border Entrepreneurship & Incubators'
    ],
    ventures: [
      {
        name: 'Döner King Denmark',
        url: 'https://www.donerking.dk',
        role: 'Founder & Managing Director',
        description: 'Pioneering fast-casual dining enterprise operating across Denmark with recognized culinary excellence.'
      },
      {
        name: 'Burger King Europe Expansion',
        role: 'Business Development Mentor',
        description: 'Strategic operations, logistics, and store expansion consultancy across premier European retail markets.'
      }
    ],
    achievements: [
      '15+ Years active mentorship in European higher education admissions',
      'Founded and scaled high-growth commercial retail brand in Denmark'
    ]
  },
  {
    id: 'suhaib-cheema',
    name: 'Suhaib Arshad Cheema',
    title: 'Director',
    roleBadge: 'UK Directorate & Enterprise Advisory',
    nationality: 'United Kingdom',
    countryCode: 'GB',
    countryFlagEmoji: '🇬🇧',
    image: '/assets/images/team/suhaib-cheema.jpg',
    featuredBadge: 'M.Sc London Met · British Enterprise Leader · 15+ Yrs UK Visas',
    quote: 'Navigating the British education ecosystem and enterprise landscape with institutional precision, unlocking lifelong careers in the United Kingdom.',
    qualifications: [
      {
        degree: 'M.Sc',
        field: 'Food Science & Enterprise',
        institution: 'London Metropolitan University',
        country: 'United Kingdom',
        highlight: true
      }
    ],
    experience: [
      '15+ years of authoritative expertise in UK immigration legislation, Skilled Worker permits, Innovator Founder visas, and Indefinite Leave to Remain (ILR).',
      'Established and heads Horizon Food (www.Horizon-foods.co.uk), an extensive nationwide distribution logistics network spanning Great Britain.',
      'High-level British enterprise consultant mentoring international graduates from top UK universities into British commercial ventures.',
      'Strategic advisor on UK university applications, UCAS protocols, Russell Group admissions, and high-value graduate sponsor licenses.'
    ],
    specializations: [
      'UK Skilled Worker & Sponsor License Advisory',
      'Innovator Founder Visas & British Settlement',
      'Nationwide Supply Chain & Distribution Logistics',
      'Russell Group Placements & UCAS Strategy'
    ],
    ventures: [
      {
        name: 'Horizon Food UK',
        url: 'https://www.horizon-foods.co.uk',
        role: 'Founder & Managing Director',
        description: 'Major UK food distribution and supply chain operator serving commercial enterprises nationwide across Britain.'
      }
    ],
    achievements: [
      '15+ Years British Immigration & Enterprise Advisory Mastery',
      'Built multi-million pound nationwide distribution network across the UK'
    ]
  },
  {
    id: 'muhammad-nawaz',
    name: 'Muhammad Nawaz',
    title: 'CEO',
    roleBadge: 'Chief Executive Officer | Human Development & Leadership Consultant',
    nationality: 'Pakistan',
    countryCode: 'PK',
    countryFlagEmoji: '🇵🇰',
    image: '/assets/images/team/muhammad-nawaz.jpg',
    featuredBadge: 'MSc UK & Punjab · 15+ Yrs Leadership · 50K+ Mentored',
    quote: 'Converting Human Potential into Human Capital. Our mission is to develop capable, confident, and purpose-driven individuals who lead themselves, advance their careers, and create a lasting positive impact on society.',
    qualifications: [
      {
        degree: 'MSc',
        field: 'Waste Management and Environmental Sciences',
        institution: 'United Kingdom Higher Education',
        country: 'United Kingdom',
        highlight: true
      },
      {
        degree: 'MSc',
        field: 'Geography',
        institution: 'University of the Punjab',
        country: 'Pakistan',
        highlight: true
      },
      {
        degree: 'M.Sc',
        field: 'Institutional Strategy & Management',
        institution: 'University of the West of Scotland (UWS)',
        country: 'United Kingdom',
        highlight: false
      }
    ],
    experience: [
      'Over 15+ years of distinguished leadership in personal development, executive training, human performance, and youth empowerment across Pakistan.',
      'Trained more than 50,000 individuals through personal development, leadership, and human development initiatives.',
      'Facilitated immersive, 3-day Life Mastery retreats for over 1,500 participants, led summer camps and youth retreats for more than 1,000 young people, and delivered hundreds of one-to-one coaching and leadership coaching sessions.',
      'Focused on helping individuals discover their potential, strengthen character, build self-confidence, develop leadership capabilities, and translate personal growth into meaningful professional and life outcomes.',
      'Committed to bridging the gap between education, personal development, and career success to empower young people for a competitive global environment.'
    ],
    specializations: [
      'Leadership Development',
      'Personal & Human Development',
      'Executive & Leadership Coaching',
      'Youth Empowerment',
      'Life Mastery & Personal Transformation',
      'Performance Development'
    ],
    achievements: [
      'Trained 50,000+ individuals across personal, leadership, and youth development',
      'Facilitated 3-Day Immersive Life Mastery Retreats for 1,500+ participants',
      'Led youth leadership retreats for 1,000+ young scholars across Pakistan',
      'Delivered hundreds of executive one-to-one leadership coaching sessions'
    ]
  }


];
