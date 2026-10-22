export interface ContinuumStage {
  id: string;
  stageNumber: string;
  name: string;
  subtitle: string;
  ageRange: string;
  description: string;
  pillars: string[];
  accreditationBadge: string;
  accreditationDetail: string;
  image: string;
}

export interface StatItem {
  id: string;
  number: number;
  suffix?: string;
  prefix?: string;
  isRatio?: boolean;
  label: string;
  description: string;
}

export interface SuccessStory {
  id: string;
  studentName: string;
  batch: string;
  university: string;
  country: string;
  program: string;
  scholarshipTier: string;
  avatar: string;
  quote: string;
}

export interface CampusFacility {
  id: string;
  title: string;
  category: string;
  description: string;
  image: string;
  features: string[];
}

export interface AdmissionStep {
  step: string;
  title: string;
  subtitle: string;
  description: string;
  timeEstimate: string;
  actionText: string;
  checklist: string[];
}

export interface NewsEvent {
  id: string;
  title: string;
  date: string;
  location: string;
  category: string;
  excerpt: string;
  image: string;
}

export interface LeadershipQualification {
  degree: string;
  field?: string;
  institution: string;
  country?: string;
  highlight?: boolean;
}

export interface LeadershipVenture {
  name: string;
  url?: string;
  role: string;
  description: string;
}

export interface LeadershipProfile {
  id: string;
  name: string;
  title: string;
  roleBadge: string;
  nationality: string;
  countryCode: 'DK' | 'GB' | 'PK' | 'GLOBAL';
  countryFlagEmoji: string;
  image: string;
  quote?: string;
  qualifications: LeadershipQualification[];
  experience: string[];
  specializations: string[];
  ventures?: LeadershipVenture[];
  achievements?: string[];
  featuredBadge?: string;
}

