import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
  GraduationCap,
  Users,
  Database,
  BarChart3,
  CheckCircle2,
  Calendar,
  Clock,
  BookOpen,
  ArrowRight,
  Sparkles,
  FileCheck,
  Send,
  Lock,
  ChevronRight,
  TrendingUp,
} from 'lucide-react';

interface LMSCRMProps {
  onOpenPortalModal: (role: 'student' | 'parent' | 'crm' | 'faculty') => void;
}

export const LMSAndCRMSection: React.FC<LMSCRMProps> = ({ onOpenPortalModal }) => {
  const [activeTab, setActiveTab] = useState<'student' | 'parent' | 'crm' | 'faculty'>('student');

  const tabs = [
    {
      id: 'student' as const,
      label: 'Student LMS Portal',
      tag: 'Virtual Learning Environment',
      icon: GraduationCap,
    },
    {
      id: 'parent' as const,
      label: 'Parent Engage Portal',
      tag: 'Live Real-Time Monitoring',
      icon: Users,
    },
    {
      id: 'crm' as const,
      label: 'Admissions CRM Engine',
      tag: 'Automated Enrollment Pipeline',
      icon: Database,
    },
    {
      id: 'faculty' as const,
      label: 'Faculty Analytics Console',
      tag: 'Curriculum & Cambridge Board Sync',
      icon: BarChart3,
    },
  ];

  return (
    <section className="py-24 sm:py-32 bg-gradient-to-b from-[#FAF7F2] via-white to-[#F0F7FF] relative overflow-hidden" id="lms-crm">
      {/* Background Decorative Tech Grid */}
      <div className="absolute inset-0 bg-[linear-gradient(to_right,#0099ff0a_1px,transparent_1px),linear-gradient(to_bottom,#0099ff0a_1px,transparent_1px)] bg-[size:3rem_3rem] pointer-events-none" />

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-16">
          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/15 border border-cyan/30 text-cyan text-xs font-display font-bold uppercase tracking-wider mb-4 shadow-sm">
            <Sparkles className="w-3.5 h-3.5 text-cyan" />
            <span>( Unified LMS & Admissions CRM Ecosystem )</span>
          </div>
          <h2 className="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-navy tracking-tight leading-[1.1] mb-6">
            The Digital Campus Engine<span className="text-cyan animate-pulse">|</span>
          </h2>
          <p className="text-slate-600 text-base sm:text-lg leading-relaxed">
            An enterprise-grade, integrated digital platform connecting scholars, parents, admissions officers, and Cambridge faculty with real-time academic intelligence, automated enrollment workflows, and rubric evaluations.
          </p>
        </div>

        {/* 4 Multi-Role Tab Switcher */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:flex items-center justify-center gap-2.5 sm:gap-3 mb-8 sm:mb-12">
          {tabs.map((tab) => {
            const Icon = tab.icon;
            const isActive = activeTab === tab.id;
            return (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`flex items-center gap-2.5 sm:gap-3 px-4 sm:px-6 py-2.5 sm:py-3.5 rounded-xl sm:rounded-2xl font-display text-xs sm:text-sm font-bold transition-all duration-300 w-full sm:w-auto ${
                  isActive
                    ? 'bg-navy text-white shadow-xl shadow-cyan/20 ring-2 ring-cyan/50 scale-102 sm:scale-105'
                    : 'bg-white text-slate-700 hover:bg-slate-50 hover:text-navy border border-navy/10 shadow-sm'
                }`}
              >
                <div
                  className={`w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl flex items-center justify-center transition-colors flex-shrink-0 ${
                    isActive ? 'bg-cyan text-white' : 'bg-cyan/10 text-cyan'
                  }`}
                >
                  <Icon className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                </div>
                <div className="text-left">
                  <div className="leading-tight text-xs sm:text-sm">{tab.label}</div>
                  <div className={`text-[9px] sm:text-[10px] font-semibold ${isActive ? 'text-cyan-light' : 'text-slate-400'}`}>
                    {tab.tag}
                  </div>
                </div>
              </button>
            );
          })}
        </div>

        {/* Dynamic Interactive Portal Dashboard Interface Container */}
        <div className="bg-[#07172C] rounded-[24px] sm:rounded-[40px] p-3.5 sm:p-8 lg:p-10 border-2 sm:border-4 border-slate-700/60 shadow-2xl relative overflow-hidden text-white">
          {/* Top Browser / OS Window Bar */}
          <div className="flex flex-wrap items-center justify-between gap-3 pb-4 sm:pb-6 mb-4 sm:mb-6 border-b border-white/10 text-xs text-white/70">
            <div className="flex items-center gap-2 sm:gap-3">
              <div className="flex items-center gap-1.5">
                <span className="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-rose-500/80" />
                <span className="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-amber-500/80" />
                <span className="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-emerald-500/80" />
              </div>
              <span className="text-white/40 hidden sm:inline">|</span>
              <span className="font-mono text-[10px] sm:text-[11px] text-cyan-light hidden sm:inline">
                https://portal.genz.edu.pk/cloud-core/{activeTab}
              </span>
            </div>

            <div className="flex items-center gap-2 sm:gap-3">
              <span className="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[9px] sm:text-[10px] font-bold">
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping" />
                Live Cloud Sync
              </span>
              <button
                onClick={() => onOpenPortalModal(activeTab)}
                className="px-3 sm:px-4 py-1.5 rounded-full bg-cyan hover:bg-cyan-light text-navy font-display font-bold text-[10px] sm:text-xs uppercase tracking-wider transition-all shadow-md"
              >
                Launch Demo Console
              </button>
            </div>
          </div>

          {/* Interactive Screen Content Based on Active Tab */}
          <AnimatePresence mode="wait">
            {activeTab === 'student' && (
              <motion.div
                key="student-lms"
                initial={{ opacity: 0, y: 15 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -15 }}
                transition={{ duration: 0.35 }}
                className="space-y-6"
              >
                {/* Header Welcome Bar */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-white/5 border border-white/10">
                  <div>
                    <div className="text-xs text-cyan-light font-bold uppercase tracking-wider">Scholar VLE Console</div>
                    <h3 className="font-display text-xl sm:text-2xl font-bold text-white">
                      Welcome back, Sarah K. (A-Level Senior Scholar)
                    </h3>
                    <p className="text-xs text-white/60">Candidate No: PK882-0492 · Cambridge AS & A-Levels Continuum</p>
                  </div>
                  <div className="flex items-center gap-6">
                    <div className="text-right">
                      <div className="text-[10px] text-white/40 uppercase">Academic GPA</div>
                      <div className="font-display text-2xl font-extrabold text-cyan">3.96 / 4.0</div>
                    </div>
                    <div className="text-right">
                      <div className="text-[10px] text-white/40 uppercase">Cohort Rank</div>
                      <div className="font-display text-2xl font-extrabold text-emerald-400">Top 2%</div>
                    </div>
                  </div>
                </div>

                {/* 3 Active Courses Cards Grid */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div className="p-5 rounded-2xl bg-white/5 border border-white/10 hover:border-cyan/40 transition-all">
                    <div className="flex items-center justify-between text-xs text-white/60 mb-2">
                      <span>Course 9702</span>
                      <span className="text-cyan font-bold">98% Complete</span>
                    </div>
                    <h4 className="font-display text-base font-bold text-white mb-2">Cambridge A-Level Physics</h4>
                    <div className="w-full bg-white/10 h-1.5 rounded-full overflow-hidden mb-3">
                      <div className="bg-cyan h-full rounded-full" style={{ width: '98%' }} />
                    </div>
                    <div className="text-[11px] text-white/70 flex items-center justify-between">
                      <span>Next: Advanced Quantum Mechanics</span>
                      <ChevronRight className="w-3.5 h-3.5 text-cyan" />
                    </div>
                  </div>

                  <div className="p-5 rounded-2xl bg-white/5 border border-white/10 hover:border-cyan/40 transition-all">
                    <div className="flex items-center justify-between text-xs text-white/60 mb-2">
                      <span>Course 9709</span>
                      <span className="text-emerald-400 font-bold">94% Complete</span>
                    </div>
                    <h4 className="font-display text-base font-bold text-white mb-2">Pure Mathematics & Mechanics</h4>
                    <div className="w-full bg-white/10 h-1.5 rounded-full overflow-hidden mb-3">
                      <div className="bg-emerald-400 h-full rounded-full" style={{ width: '94%' }} />
                    </div>
                    <div className="text-[11px] text-white/70 flex items-center justify-between">
                      <span>Next: Multivariable Integration</span>
                      <ChevronRight className="w-3.5 h-3.5 text-cyan" />
                    </div>
                  </div>

                  <div className="p-5 rounded-2xl bg-white/5 border border-white/10 hover:border-cyan/40 transition-all">
                    <div className="flex items-center justify-between text-xs text-white/60 mb-2">
                      <span>Course 9618</span>
                      <span className="text-amber-400 font-bold">96% Complete</span>
                    </div>
                    <h4 className="font-display text-base font-bold text-white mb-2">Computer Science & AI Labs</h4>
                    <div className="w-full bg-white/10 h-1.5 rounded-full overflow-hidden mb-3">
                      <div className="bg-amber-400 h-full rounded-full" style={{ width: '96%' }} />
                    </div>
                    <div className="text-[11px] text-white/70 flex items-center justify-between">
                      <span>Next: Neural Network Ethics</span>
                      <ChevronRight className="w-3.5 h-3.5 text-cyan" />
                    </div>
                  </div>
                </div>

                {/* Today's Schedule & Assignment Submissions */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="p-5 rounded-2xl bg-white/5 border border-white/10">
                    <div className="flex items-center gap-2 text-cyan font-bold text-xs uppercase tracking-wider mb-4">
                      <Clock className="w-4 h-4" />
                      <span>Today's Live Class Timetable</span>
                    </div>
                    <div className="space-y-3">
                      <div className="flex items-center justify-between p-3 rounded-xl bg-white/5 text-xs">
                        <div>
                          <div className="font-bold text-white">09:30 AM – 10:45 AM</div>
                          <div className="text-white/60">Physics Lab 3 (Optics & Laser Interferometry)</div>
                        </div>
                        <span className="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 font-bold">In Session</span>
                      </div>
                      <div className="flex items-center justify-between p-3 rounded-xl bg-white/5 text-xs">
                        <div>
                          <div className="font-bold text-white">11:15 AM – 12:30 PM</div>
                          <div className="text-white/60">Mathematics Colloquium · Room 204</div>
                        </div>
                        <span className="px-2.5 py-1 rounded-full bg-cyan/20 text-cyan font-bold">Upcoming</span>
                      </div>
                    </div>
                  </div>

                  <div className="p-5 rounded-2xl bg-white/5 border border-white/10">
                    <div className="flex items-center gap-2 text-cyan font-bold text-xs uppercase tracking-wider mb-4">
                      <FileCheck className="w-4 h-4" />
                      <span>Assignment Dropboxes & Rubric Feedback</span>
                    </div>
                    <div className="space-y-3">
                      <div className="flex items-center justify-between p-3 rounded-xl bg-white/5 text-xs">
                        <div>
                          <div className="font-bold text-white">Quantum Duality Diagnostic Essay</div>
                          <div className="text-white/60">Graded by Dr. Jonathan Croft · Band 1 (A*)</div>
                        </div>
                        <span className="text-emerald-400 font-bold">Score: 98/100</span>
                      </div>
                      <div className="flex items-center justify-between p-3 rounded-xl bg-white/5 text-xs">
                        <div>
                          <div className="font-bold text-white">Differential Calculus Problem Set 4</div>
                          <div className="text-white/60">Submitted on time · Awaiting Teacher Verification</div>
                        </div>
                        <span className="text-amber-400 font-bold">Pending Review</span>
                      </div>
                    </div>
                  </div>
                </div>
              </motion.div>
            )}

            {activeTab === 'parent' && (
              <motion.div
                key="parent-portal"
                initial={{ opacity: 0, y: 15 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -15 }}
                transition={{ duration: 0.35 }}
                className="space-y-6"
              >
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-white/5 border border-white/10">
                  <div>
                    <div className="text-xs text-cyan-light font-bold uppercase tracking-wider">Guardian Engage Gateway</div>
                    <h3 className="font-display text-xl sm:text-2xl font-bold text-white">
                      Mr. & Mrs. Tariq (Parents of Sarah K.)
                    </h3>
                    <p className="text-xs text-white/60">Islamabad Diplomatic Enclave Campus · Account: GENZ-PRNT-8812</p>
                  </div>
                  <div className="flex items-center gap-4">
                    <span className="px-3.5 py-1.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-bold flex items-center gap-1.5">
                      <CheckCircle2 className="w-4 h-4" />
                      <span>Biometric Checked-In (08:14 AM)</span>
                    </span>
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                  <div className="p-4 rounded-2xl bg-white/5 border border-white/10">
                    <div className="text-xs text-white/50 mb-1">Term Attendance Rate</div>
                    <div className="font-display text-2xl font-extrabold text-cyan">99.2%</div>
                    <div className="text-[11px] text-emerald-400 font-semibold mt-1">Perfect punctuality record</div>
                  </div>
                  <div className="p-4 rounded-2xl bg-white/5 border border-white/10">
                    <div className="text-xs text-white/50 mb-1">Cambridge Predicted Grades</div>
                    <div className="font-display text-2xl font-extrabold text-emerald-400">A* A* A* A</div>
                    <div className="text-[11px] text-white/60 mt-1">Official Mock Board Result</div>
                  </div>
                  <div className="p-4 rounded-2xl bg-white/5 border border-white/10">
                    <div className="text-xs text-white/50 mb-1">Pastoral Behavior Score</div>
                    <div className="font-display text-2xl font-extrabold text-purple-400">Distinction</div>
                    <div className="text-[11px] text-white/60 mt-1">Head of Senior School Commendation</div>
                  </div>
                  <div className="p-4 rounded-2xl bg-white/5 border border-white/10">
                    <div className="text-xs text-white/50 mb-1">Fee & Scholarship Account</div>
                    <div className="font-display text-2xl font-extrabold text-white">Paid & Clear</div>
                    <div className="text-[11px] text-cyan-light mt-1">50% Merit Scholarship Applied</div>
                  </div>
                </div>

                <div className="p-5 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-between flex-wrap gap-4">
                  <div>
                    <h4 className="font-display text-base font-bold text-white mb-1">Direct Pastoral Dialogue & Teacher Messaging</h4>
                    <p className="text-xs text-white/60">Schedule direct virtual or on-campus meetings with Head of Department or University Guidance Counselors.</p>
                  </div>
                  <button
                    onClick={() => onOpenPortalModal('parent')}
                    className="px-6 py-2.5 rounded-full bg-cyan hover:bg-cyan-light text-navy font-display font-bold text-xs uppercase tracking-wider transition-all"
                  >
                    Open Parent Console
                  </button>
                </div>
              </motion.div>
            )}

            {activeTab === 'crm' && (
              <motion.div
                key="admissions-crm"
                initial={{ opacity: 0, y: 15 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -15 }}
                transition={{ duration: 0.35 }}
                className="space-y-6"
              >
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-white/5 border border-white/10">
                  <div>
                    <div className="text-xs text-cyan-light font-bold uppercase tracking-wider">Admissions Officer Pipeline</div>
                    <h3 className="font-display text-xl sm:text-2xl font-bold text-white">
                      Admissions Lead CRM & Applicant Pipeline
                    </h3>
                    <p className="text-xs text-white/60">Academic Year 2026–27 Intake · Islamabad & Lahore Campuses</p>
                  </div>
                  <div className="flex items-center gap-3">
                    <span className="px-3.5 py-1 rounded-full bg-cyan/20 text-cyan-light font-bold text-xs">
                      142 Active Applicants
                    </span>
                  </div>
                </div>

                {/* Visual Kanban Stages Pipeline */}
                <div className="grid grid-cols-2 md:grid-cols-5 gap-3">
                  <div className="p-4 rounded-2xl bg-white/5 border border-white/10">
                    <div className="text-xs font-bold text-amber-400 mb-1">1. Inquiries</div>
                    <div className="font-display text-2xl font-extrabold text-white">142</div>
                    <div className="text-[11px] text-white/60 mt-1">Web & Tour Leads</div>
                  </div>
                  <div className="p-4 rounded-2xl bg-white/5 border border-white/10">
                    <div className="text-xs font-bold text-orange-400 mb-1">2. Diagnostic Test</div>
                    <div className="font-display text-2xl font-extrabold text-white">89</div>
                    <div className="text-[11px] text-white/60 mt-1">Cognitive Assessment</div>
                  </div>
                  <div className="p-4 rounded-2xl bg-white/5 border border-white/10">
                    <div className="text-xs font-bold text-sky-400 mb-1">3. Interview</div>
                    <div className="font-display text-2xl font-extrabold text-white">45</div>
                    <div className="text-[11px] text-white/60 mt-1">Principal Dialogue</div>
                  </div>
                  <div className="p-4 rounded-2xl bg-white/5 border border-white/10">
                    <div className="text-xs font-bold text-purple-400 mb-1">4. Offer Sent</div>
                    <div className="font-display text-2xl font-extrabold text-white">32</div>
                    <div className="text-[11px] text-white/60 mt-1">Formal Letter Issued</div>
                  </div>
                  <div className="p-4 rounded-2xl bg-white/5 border border-white/10">
                    <div className="text-xs font-bold text-emerald-400 mb-1">5. Enrolled</div>
                    <div className="font-display text-2xl font-extrabold text-white">28</div>
                    <div className="text-[11px] text-white/60 mt-1">Confirmed Cohort</div>
                  </div>
                </div>

                {/* Automated CRM Features Checklist */}
                <div className="p-5 rounded-2xl bg-white/5 border border-white/10 grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div className="flex items-start gap-2.5">
                    <CheckCircle2 className="w-4 h-4 text-cyan flex-shrink-0 mt-0.5" />
                    <div>
                      <div className="font-bold text-xs text-white">Automated Lead Scoring</div>
                      <div className="text-[11px] text-white/60">Instant assessment of student eligibility & Cambridge fit.</div>
                    </div>
                  </div>
                  <div className="flex items-start gap-2.5">
                    <CheckCircle2 className="w-4 h-4 text-cyan flex-shrink-0 mt-0.5" />
                    <div>
                      <div className="font-bold text-xs text-white">Omnichannel Follow-up</div>
                      <div className="text-[11px] text-white/60">Automated WhatsApp, SMS, and Email interview scheduling.</div>
                    </div>
                  </div>
                  <div className="flex items-start gap-2.5">
                    <CheckCircle2 className="w-4 h-4 text-cyan flex-shrink-0 mt-0.5" />
                    <div>
                      <div className="font-bold text-xs text-white">E-Sign Digital Contracts</div>
                      <div className="text-[11px] text-white/60">Seamless paperless enrollment and automated fee invoicing.</div>
                    </div>
                  </div>
                </div>
              </motion.div>
            )}

            {activeTab === 'faculty' && (
              <motion.div
                key="faculty-console"
                initial={{ opacity: 0, y: 15 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -15 }}
                transition={{ duration: 0.35 }}
                className="space-y-6"
              >
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-white/5 border border-white/10">
                  <div>
                    <div className="text-xs text-cyan-light font-bold uppercase tracking-wider">Cambridge Faculty Academic Console</div>
                    <h3 className="font-display text-xl sm:text-2xl font-bold text-white">
                      Curriculum Analytics & Exam Board Direct Sync
                    </h3>
                    <p className="text-xs text-white/60">Authorised Center: PK882 · Cambridge International Direct Link</p>
                  </div>
                  <div className="flex items-center gap-3">
                    <span className="px-3.5 py-1.5 rounded-full bg-cyan/20 text-cyan-light font-bold text-xs flex items-center gap-2">
                      <TrendingUp className="w-4 h-4" />
                      <span>Cambridge Exam Board Sync: 100%</span>
                    </span>
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div className="p-5 rounded-2xl bg-white/5 border border-white/10">
                    <div className="text-xs text-cyan font-bold uppercase tracking-wider mb-2">Class Mastery Level</div>
                    <div className="font-display text-3xl font-extrabold text-white mb-1">94.8%</div>
                    <p className="text-xs text-white/60">Across 18 Cambridge secondary & higher secondary streams.</p>
                  </div>
                  <div className="p-5 rounded-2xl bg-white/5 border border-white/10">
                    <div className="text-xs text-emerald-400 font-bold uppercase tracking-wider mb-2">Early-Intervention Flag</div>
                    <div className="font-display text-3xl font-extrabold text-white mb-1">0 At-Risk</div>
                    <p className="text-xs text-white/60">Adaptive diagnostic quizzes prevent learning gaps before term exams.</p>
                  </div>
                  <div className="p-5 rounded-2xl bg-white/5 border border-white/10">
                    <div className="text-xs text-purple-400 font-bold uppercase tracking-wider mb-2">Syllabus Progress Tracker</div>
                    <div className="font-display text-3xl font-extrabold text-white mb-1">3 Weeks Ahead</div>
                    <p className="text-xs text-white/60">Allows ample time for past-paper drills and mock exam series.</p>
                  </div>
                </div>
              </motion.div>
            )}
          </AnimatePresence>

          {/* Bottom Callout & Live Demo Trigger */}
          <div className="mt-8 pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div className="flex items-center gap-3 text-xs text-white/70">
              <Lock className="w-4 h-4 text-cyan" />
              <span>Protected by 256-bit SSL encryption & Role-Based Access Control (RBAC).</span>
            </div>
            <button
              onClick={() => onOpenPortalModal(activeTab)}
              className="inline-flex items-center gap-2 px-7 py-3 bg-gradient-to-r from-cyan to-blue-600 hover:from-cyan-light hover:to-blue-500 text-white rounded-full font-display font-bold text-xs uppercase tracking-wider shadow-lg hover:shadow-cyan-glow transition-all transform hover:scale-105"
            >
              <span>Explore Live {tabs.find((t) => t.id === activeTab)?.label}</span>
              <ArrowRight className="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </section>
  );
};
