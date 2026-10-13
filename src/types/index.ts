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
