import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
  X,
  Lock,
  GraduationCap,
  Users,
  Database,
  BarChart3,
  CheckCircle2,
  ArrowRight,
  Shield,
  KeyRound,
  ExternalLink,
} from 'lucide-react';

interface PortalModalProps {
  isOpen: boolean;
  onClose: () => void;
  initialRole?: 'student' | 'parent' | 'crm' | 'faculty';
}

export const PortalModal: React.FC<PortalModalProps> = ({
  isOpen,
  onClose,
  initialRole = 'student',
}) => {
  const [role, setRole] = useState<'student' | 'parent' | 'crm' | 'faculty'>(initialRole);
  const [loggedIn, setLoggedIn] = useState(false);

  // Sync role if initialRole changes
  React.useEffect(() => {
    setRole(initialRole);
    setLoggedIn(false);
  }, [initialRole, isOpen]);

  const roleData = {
    student: {
      title: 'Student LMS Virtual Learning Environment',
      email: 'sarah.k@student.genz.edu.pk',
      id: 'PK882-0492 · Cambridge A-Level Scholar',
      stats: [
        { label: 'Cumulative GPA', value: '3.96 / 4.0' },
        { label: 'Active Courses', value: '4 Cambridge Units' },
        { label: 'Attendance', value: '99.4%' },
      ],
      notice: 'Physics Lab 3 Assignment due today at 11:59 PM. Cambridge mock series begins in 2 weeks.',
    },
    parent: {
      title: 'Parent & Guardian 24/7 Engage Portal',
      email: 'tariq.fam@parent.genz.edu.pk',
      id: 'Account: GENZ-PRNT-8812 · Student: Sarah K.',
      stats: [
        { label: 'Biometric Status', value: 'Checked-In (08:14 AM)' },
        { label: 'Predicted Grades', value: 'A* A* A* A' },
        { label: 'Fee Status', value: 'Clear & Verified' },
      ],
      notice: 'Pastoral report for Quarter 1 published. Parent-Teacher Symposium scheduled for Friday 4:00 PM.',
    },
    crm: {
      title: 'Admissions Lead CRM & Applicant Pipeline',
      email: 'admissions.crm@genz.edu.pk',
      id: 'Admin Clearance: Level 4 · Intake 2026–27',
      stats: [
        { label: 'Active Pipeline', value: '142 Applicants' },
        { label: 'Diagnostic Tests', value: '89 Completed' },
        { label: 'Offers Issued', value: '32 Letters' },
      ],
      notice: '3 new scholarship inquiries received in last 2 hours. Automated diagnostic test links dispatched.',
    },
    faculty: {
      title: 'Cambridge Faculty Academic Command Console',
      email: 'j.croft@faculty.genz.edu.pk',
      id: 'Dr. Jonathan Croft · Head of Sciences (PK882)',
      stats: [
        { label: 'Class Mastery', value: '94.8%' },
        { label: 'Board Sync', value: 'Cambridge PK882 Live' },
        { label: 'At-Risk Flags', value: '0 Students' },
      ],
      notice: 'Cambridge International AS & A Level Physics syllabus updates synced for term 2.',
    },
  };

  const current = roleData[role];

  return (
    <AnimatePresence>
      {isOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
          {/* Backdrop */}
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={onClose}
            className="fixed inset-0 bg-navy-dark/80 backdrop-blur-md"
          />

          {/* Modal Container */}
          <motion.div
            initial={{ opacity: 0, scale: 0.95, y: 20 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.95, y: 20 }}
            className="relative w-full max-w-2xl bg-[#07172C] border-2 border-cyan/30 rounded-3xl p-6 sm:p-8 shadow-2xl text-white z-10 overflow-hidden"
          >
            {/* Top Close Button */}
            <button
              onClick={onClose}
              className="absolute top-6 right-6 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors"
            >
              <X className="w-5 h-5" />
            </button>

            {/* Modal Header */}
            <div className="mb-6">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan/15 border border-cyan/30 text-cyan-light text-xs font-bold uppercase tracking-wider mb-2">
                <Lock className="w-3.5 h-3.5" />
                <span>Unified Cloud Gateway</span>
              </div>
              <h3 className="font-display text-2xl sm:text-3xl font-bold text-white">
                GenZ LMS & CRM Platform
              </h3>
              <p className="text-white/60 text-xs sm:text-sm">
                Select your role to access the virtual learning environment or admissions management pipeline.
              </p>
            </div>

            {/* 4 Role Selector Tabs */}
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-6">
              <button
                onClick={() => {
                  setRole('student');
                  setLoggedIn(false);
                }}
                className={`p-3 rounded-xl border text-center transition-all flex flex-col items-center gap-1.5 ${
                  role === 'student'
                    ? 'bg-cyan text-navy border-cyan font-bold shadow-lg shadow-cyan/30'
                    : 'bg-white/5 border-white/10 text-white/70 hover:bg-white/10'
                }`}
              >
                <GraduationCap className="w-5 h-5" />
                <span className="text-xs">Student LMS</span>
              </button>

              <button
                onClick={() => {
                  setRole('parent');
                  setLoggedIn(false);
                }}
                className={`p-3 rounded-xl border text-center transition-all flex flex-col items-center gap-1.5 ${
                  role === 'parent'
                    ? 'bg-cyan text-navy border-cyan font-bold shadow-lg shadow-cyan/30'
                    : 'bg-white/5 border-white/10 text-white/70 hover:bg-white/10'
                }`}
              >
                <Users className="w-5 h-5" />
                <span className="text-xs">Parent Portal</span>
              </button>

              <button
                onClick={() => {
                  setRole('crm');
                  setLoggedIn(false);
                }}
                className={`p-3 rounded-xl border text-center transition-all flex flex-col items-center gap-1.5 ${
                  role === 'crm'
                    ? 'bg-cyan text-navy border-cyan font-bold shadow-lg shadow-cyan/30'
                    : 'bg-white/5 border-white/10 text-white/70 hover:bg-white/10'
                }`}
              >
                <Database className="w-5 h-5" />
                <span className="text-xs">Admissions CRM</span>
              </button>

              <button
                onClick={() => {
                  setRole('faculty');
                  setLoggedIn(false);
                }}
                className={`p-3 rounded-xl border text-center transition-all flex flex-col items-center gap-1.5 ${
                  role === 'faculty'
                    ? 'bg-cyan text-navy border-cyan font-bold shadow-lg shadow-cyan/30'
                    : 'bg-white/5 border-white/10 text-white/70 hover:bg-white/10'
                }`}
              >
                <BarChart3 className="w-5 h-5" />
                <span className="text-xs">Faculty Hub</span>
              </button>
            </div>

            {/* Login or Live Dashboard View */}
            {!loggedIn ? (
              <div className="p-6 rounded-2xl bg-white/5 border border-white/10 space-y-4">
                <div className="text-xs font-bold uppercase tracking-wider text-cyan-light">
                  {current.title}
                </div>

                <div>
                  <label className="block text-xs font-semibold text-white/70 mb-1">
                    Demo Account Email / Identity
                  </label>
                  <input
                    type="text"
                    readOnly
                    value={current.email}
                    className="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white text-xs font-mono select-all focus:outline-none"
                  />
                  <div className="text-[11px] text-white/50 mt-1">{current.id}</div>
                </div>

                <div>
                  <label className="block text-xs font-semibold text-white/70 mb-1">
                    Security Passkey / 2FA Token
                  </label>
                  <div className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white text-xs font-mono">
                    <KeyRound className="w-4 h-4 text-cyan" />
                    <span>•••••••••••• (Authorized Single Sign-On Active)</span>
                  </div>
                </div>

                <div className="pt-2 flex items-center justify-between">
                  <span className="text-xs text-emerald-400 flex items-center gap-1.5 font-semibold">
                    <Shield className="w-3.5 h-3.5" />
                    <span>256-Bit SSL Single Sign-On Verified</span>
                  </span>

                  <button
                    onClick={() => setLoggedIn(true)}
                    className="px-6 py-2.5 rounded-full bg-cyan hover:bg-cyan-light text-navy font-display font-bold text-xs uppercase tracking-wider transition-all shadow-lg hover:shadow-cyan-glow flex items-center gap-2"
                  >
                    <span>Launch Live Console</span>
                    <ArrowRight className="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>
            ) : (
              <motion.div
                initial={{ opacity: 0, scale: 0.98 }}
                animate={{ opacity: 1, scale: 1 }}
                className="p-6 rounded-2xl bg-white/5 border border-cyan/40 space-y-5"
              >
                <div className="flex items-center justify-between pb-3 border-b border-white/10">
                  <div>
                    <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-bold">
                      <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping" />
                      Session Connected
                    </span>
                    <h4 className="font-display font-bold text-lg text-white mt-1">
                      {current.title}
                    </h4>
                  </div>
                  <button
                    onClick={() => setLoggedIn(false)}
                    className="text-xs text-cyan-light hover:underline font-semibold"
                  >
                    Switch Account
                  </button>
                </div>

                {/* Live Role Stats */}
                <div className="grid grid-cols-3 gap-3">
                  {current.stats.map((s) => (
                    <div key={s.label} className="p-3 rounded-xl bg-white/5 border border-white/10">
                      <div className="text-[10px] text-white/50">{s.label}</div>
                      <div className="font-display text-base font-extrabold text-white mt-0.5">
                        {s.value}
                      </div>
                    </div>
                  ))}
                </div>

                {/* Live System Broadcast Alert */}
                <div className="p-3.5 rounded-xl bg-cyan/10 border border-cyan/25 flex items-start gap-2.5 text-xs text-white/80">
                  <CheckCircle2 className="w-4 h-4 text-cyan flex-shrink-0 mt-0.5" />
                  <div>
                    <span className="font-bold text-cyan-light">Live Notification: </span>
                    <span>{current.notice}</span>
                  </div>
                </div>

                <div className="pt-2 flex items-center justify-between">
                  <span className="text-xs text-white/60">
                    GenZ Cloud Core v3.8.4
                  </span>
                  <button
                    onClick={onClose}
                    className="px-6 py-2.5 rounded-full bg-white text-navy hover:bg-cyan hover:text-white font-display font-bold text-xs uppercase tracking-wider transition-all"
                  >
                    Done & Return
                  </button>
                </div>
              </motion.div>
            )}
          </motion.div>
        </div>
      )}
    </AnimatePresence>
  );
};
