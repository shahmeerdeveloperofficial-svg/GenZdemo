import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
  Quote,
  Sparkles,
  GraduationCap,
  Briefcase,
  ExternalLink,
  ShieldCheck,
  Building2,
  CheckCircle2,
  MapPin,
  ArrowRight,
  X,
  Award,
  Globe
} from 'lucide-react';
import { LEADERSHIP_PROFILES } from '../data/leadershipData';
import { LeadershipProfile } from '../types';
import { DenmarkFlag, UKFlag, PakistanFlag } from './CountryFlags';

interface LeadershipSectionProps {
  onOpenConsultation?: (profile?: LeadershipProfile) => void;
}

export const LeadershipSection: React.FC<LeadershipSectionProps> = ({ onOpenConsultation }) => {
  const [selectedFilter, setSelectedFilter] = useState<'ALL' | 'DK' | 'GB' | 'PK'>('ALL');
  const [activeDossier, setActiveDossier] = useState<LeadershipProfile | null>(null);
  const [hoveredCardId, setHoveredCardId] = useState<string | null>(null);

  const filteredProfiles = LEADERSHIP_PROFILES.filter((p) => {
    if (selectedFilter === 'ALL') return true;
    return p.countryCode === selectedFilter;
  });

  return (
    <section
      className="py-24 md:py-32 bg-gradient-to-b from-[#07172C] via-[#0B2545] to-[#040E1B] relative overflow-hidden text-white border-b border-cyan/20"
      id="leadership"
    >
      {/* Ambient Logo Theme Backdrops */}
      <div className="absolute inset-0 pointer-events-none overflow-hidden">
        {/* Cyan Ambient Spotlight */}
        <div className="absolute top-1/4 -left-40 w-[35rem] h-[35rem] bg-cyan/15 rounded-full blur-[160px] animate-pulse" />
        {/* Blue Orb Bottom Right */}
        <div className="absolute bottom-1/4 -right-40 w-[35rem] h-[35rem] bg-blue-600/15 rounded-full blur-[160px]" />
        {/* Tech Grid */}
        <div
          className="absolute inset-0 opacity-[0.03]"
          style={{
            backgroundImage: `radial-gradient(circle at 1.5px 1.5px, #0099FF 1px, transparent 0)`,
            backgroundSize: '40px 40px'
          }}
        />
      </div>

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-16">
          <motion.div
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/15 border border-cyan/40 text-cyan-light text-xs font-display font-bold uppercase tracking-widest shadow-[0_0_20px_rgba(0,153,255,0.35)] mb-4"
          >
            <Sparkles className="w-3.5 h-3.5 text-cyan animate-spin-slow" />
            <span>Executive Directorate & Global Governance</span>
          </motion.div>

          <motion.h2
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.1 }}
            className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-[1.18] mb-5"
          >
            Global Advisory Board & Executive Directorate<span className="heading-cursor-line">|</span>
          </motion.h2>

          <motion.p
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.2 }}
            className="text-slate-300 text-sm sm:text-base leading-relaxed"
          >
            Guided by European doctoral scholars, British enterprise directors, and international fellows from Aarhus, Copenhagen, Bangor, and London — mentoring scholars into world powerhouses.
          </motion.p>

          {/* Interactive Filter Pills */}
          <motion.div
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.3 }}
            className="flex flex-wrap items-center justify-center gap-2 sm:gap-3 mt-8"
          >
            {[
              { id: 'ALL', label: 'All Directorate', count: 4, icon: Globe },
              { id: 'DK', label: 'Denmark Directorate', count: 2, flag: '🇩🇰' },
              { id: 'GB', label: 'United Kingdom Directorate', count: 1, flag: '🇬🇧' },
              { id: 'PK', label: 'Pakistan Strategic HQ', count: 1, flag: '🇵🇰' }
            ].map((tab) => {
              const active = selectedFilter === tab.id;
              const IconComp = tab.icon;
              return (
                <button
                  key={tab.id}
                  onClick={() => setSelectedFilter(tab.id as any)}
                  className={`px-4 py-2 rounded-xl text-xs sm:text-sm font-display font-semibold transition-all duration-300 flex items-center gap-2 ${
                    active
                      ? 'bg-gradient-to-r from-cyan via-blue-500 to-cyan text-white shadow-[0_0_25px_rgba(0,153,255,0.45)] scale-105'
                      : 'bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 hover:border-cyan/40'
                  }`}
                >
                  {tab.flag ? (
                    <span className="text-base">{tab.flag}</span>
                  ) : IconComp ? (
                    <IconComp className="w-4 h-4 text-cyan" />
                  ) : null}
                  <span>{tab.label}</span>
                  <span
                    className={`ml-1 text-[11px] px-1.5 py-0.2 rounded-full ${
                      active ? 'bg-white/20 text-white' : 'bg-white/10 text-slate-400'
                    }`}
                  >
                    {tab.count}
                  </span>
                </button>
              );
            })}
          </motion.div>
        </div>

        {/* 2-Column Grid of Jeff Bezos / Arch Style Executive Cards */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <AnimatePresence mode="popLayout">
            {filteredProfiles.map((profile, index) => (
              <DirectorPillCard
                key={profile.id}
                profile={profile}
                index={index}
                isHovered={hoveredCardId === profile.id}
                onHover={() => setHoveredCardId(profile.id)}
                onLeave={() => setHoveredCardId(null)}
                onOpenDossier={() => setActiveDossier(profile)}
              />
            ))}
          </AnimatePresence>
        </div>

        {/* Bottom Banner */}
        <motion.div
          initial={{ opacity: 0, y: 25 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="mt-16 p-8 rounded-3xl bg-gradient-to-r from-white/[0.06] via-cyan/[0.08] to-white/[0.04] border border-cyan/30 backdrop-blur-xl relative overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)]"
        >
          <div className="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
            <div className="text-center md:text-left">
              <span className="text-cyan text-xs font-display font-bold uppercase tracking-wider block mb-1">
                Direct Mentorship & Scholarship Advisory
              </span>
              <h3 className="font-display text-2xl font-extrabold text-white">
                Connect Directly with our European & British Directorate
              </h3>
              <p className="text-slate-300 text-sm mt-1">
                Consult with our directors for Erasmus Mundus scholarships, Nordic admissions, and UK visas.
              </p>
            </div>
            <button
              onClick={() => onOpenConsultation?.()}
              className="shrink-0 px-7 py-3.5 rounded-xl bg-gradient-to-r from-cyan via-blue-500 to-cyan bg-[length:200%_auto] hover:bg-right font-display text-xs sm:text-sm font-bold text-white shadow-[0_0_30px_rgba(0,153,255,0.45)] hover:scale-105 active:scale-95 transition-all flex items-center gap-2"
            >
              <Sparkles className="w-4 h-4 text-white" />
              <span>Book Directorate Advisory</span>
              <ArrowRight className="w-4 h-4" />
            </button>
          </div>
        </motion.div>
      </div>

      {/* Interactive Dossier Modal */}
      <AnimatePresence>
        {activeDossier && (
          <ExecutiveDossierModal
            profile={activeDossier}
            onClose={() => setActiveDossier(null)}
            onConsult={() => {
              const current = activeDossier;
              setActiveDossier(null);
              onOpenConsultation?.(current);
            }}
          />
        )}
      </AnimatePresence>
    </section>
  );
};

/* -------------------------------------------------------------------------- */
/* JEFF BEZOS ARCH / PILL STYLE EXECUTIVE CARD                               */
/* -------------------------------------------------------------------------- */
interface DirectorPillCardProps {
  profile: LeadershipProfile;
  index: number;
  isHovered: boolean;
  onHover: () => void;
  onLeave: () => void;
  onOpenDossier: () => void;
}

const DirectorPillCard: React.FC<DirectorPillCardProps> = ({
  profile,
  index,
  isHovered,
  onHover,
  onLeave,
  onOpenDossier
}) => {
  // Custom zoom and focal position for each photo to keep faces crystal-clear
  const getImageStyling = () => {
    switch (profile.id) {
      case 'zahid-bajwa':
        return 'object-cover object-[50%_25%] scale-[1.18]';
      case 'muzaffar-hussain':
        return 'object-cover object-[50%_28%] scale-[1.18]';
      case 'suhaib-cheema':
        return 'object-cover object-[52%_15%] scale-[1.95] origin-[52%_15%]';
      case 'muhammad-nawaz':
        return 'object-cover object-[29%_16%] scale-[1.85] origin-[29%_16%]';
      default:
        return 'object-cover object-top';
    }
  };

  const renderFlag = () => {
    if (profile.countryCode === 'DK') return <DenmarkFlag className="w-4 h-3 rounded-sm shadow-sm" />;
    if (profile.countryCode === 'GB') return <UKFlag className="w-4 h-3 rounded-sm shadow-sm" />;
    if (profile.countryCode === 'PK') return <PakistanFlag className="w-4 h-3 rounded-sm shadow-sm" />;
    return <span className="text-xs">{profile.countryFlagEmoji}</span>;
  };

  return (
    <motion.div
      layout
      initial={{ opacity: 0, y: 25 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, scale: 0.95 }}
      transition={{ duration: 0.4, delay: index * 0.08 }}
      onMouseEnter={onHover}
      onMouseLeave={onLeave}
      className="relative group h-full"
    >
      {/* Decorative Peeking Circle Motif behind card (as in Jeff Bezos reference) */}
      <div
        className={`absolute -top-4 -right-4 w-32 h-32 rounded-full transition-all duration-500 pointer-events-none ${
          isHovered
            ? 'bg-cyan/25 blur-xl scale-125'
            : 'bg-cyan/10 blur-2xl scale-100'
        }`}
      />

      {/* Main Crisp Card Container (High Contrast, International Corporate Aesthetic) */}
      <div
        className={`relative h-full rounded-[2rem] sm:rounded-[2.5rem] bg-white text-navy p-6 sm:p-7 shadow-[0_20px_50px_rgba(0,0,0,0.35)] transition-all duration-300 flex flex-col justify-between border-2 ${
          isHovered
            ? 'border-cyan shadow-[0_25px_60px_rgba(0,153,255,0.35)] -translate-y-1'
            : 'border-white/80 hover:border-cyan/40'
        }`}
      >
        {/* Top/Body Grid: Capsule Pill Arch on Left + Quotation & Content on Right */}
        <div className="flex flex-col sm:flex-row items-center sm:items-start gap-6">
          {/* LEFT: Stadium Pill Arch (Vertical Capsule) holding Portrait */}
          <div className="shrink-0 flex flex-col items-center">
            <div
              className={`relative w-40 sm:w-44 md:w-48 aspect-[1/1.45] rounded-t-full rounded-b-full overflow-hidden shadow-xl border-4 transition-all duration-500 bg-gradient-to-b from-navy-dark via-navy to-navy-deep ${
                isHovered
                  ? 'border-cyan ring-4 ring-cyan/25 scale-105'
                  : 'border-slate-100 ring-2 ring-navy/10'
              }`}
            >
              <img
                src={profile.image}
                alt={profile.name}
                className={`w-full h-full filter brightness-105 contrast-105 transition-transform duration-700 ${getImageStyling()}`}
              />

              {/* Bottom Subtle Vignette inside Arch */}
              <div className="absolute inset-0 bg-gradient-to-t from-navy/60 via-transparent to-transparent pointer-events-none" />

              {/* Pill Arch Country Badge at Bottom */}
              <div className="absolute bottom-3 left-1/2 -translate-x-1/2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-navy-deep/90 backdrop-blur-md border border-white/20 text-white text-[10px] font-semibold whitespace-nowrap shadow-md">
                {renderFlag()}
                <span>{profile.nationality}</span>
              </div>
            </div>

            {/* Official Designation Pill beneath Arch */}
            <div className="mt-3 px-3 py-1 rounded-full bg-cyan/10 border border-cyan/30 text-cyan font-display font-bold text-[11px] uppercase tracking-wider text-center">
              {profile.title}
            </div>
          </div>

          {/* RIGHT: Double Quote Icon, Vision Text with Hover Illumination, and Credentials */}
          <div className="flex-1 flex flex-col justify-between min-w-0">
            {/* Quotation Icon & Message with Interactive Hover Effects */}
            <div
              className={`p-4 rounded-2xl transition-all duration-300 mb-4 ${
                isHovered
                  ? 'bg-cyan/5 border-l-4 border-cyan'
                  : 'bg-slate-50 border-l-4 border-slate-200'
              }`}
            >
              {/* Double Quote Glyph */}
              <Quote
                className={`w-8 h-8 mb-2 transition-all duration-300 ${
                  isHovered ? 'text-cyan scale-110' : 'text-slate-400'
                }`}
              />

              {/* Authentic Statement/Quote */}
              <p
                className={`text-xs sm:text-sm leading-relaxed transition-colors duration-300 ${
                  isHovered ? 'text-navy font-medium' : 'text-slate-600'
                }`}
              >
                "{profile.quote || profile.experience[0]}"
              </p>
            </div>

            {/* Executive Name & Title (Jeff Bezos Signature Placement) */}
            <div className="mb-3">
              <h3 className="font-display text-xl sm:text-2xl font-black text-navy uppercase tracking-tight group-hover:text-cyan transition-colors">
                {profile.name}
              </h3>
              <p className="text-xs font-semibold text-slate-500 mt-0.5">
                {profile.roleBadge}
              </p>
            </div>

            {/* Verified Degrees List (Clean & Readable, No Truncation) */}
            <div className="space-y-1.5 pt-2 border-t border-slate-100">
              {profile.qualifications.slice(0, 2).map((q, qIdx) => (
                <div key={qIdx} className="flex items-start gap-1.5 text-xs text-slate-700">
                  <CheckCircle2 className="w-3.5 h-3.5 text-cyan shrink-0 mt-0.5" />
                  <span className="leading-snug">
                    <strong className="text-navy font-bold">{q.degree}:</strong> {q.institution}
                    {q.country ? ` (${q.country})` : ''}
                  </span>
                </div>
              ))}
            </div>

            {/* Commercial Venture or Special Feature Pill (if present) */}
            {profile.ventures && profile.ventures.length > 0 && (
              <div className="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-xs font-medium">
                <Building2 className="w-3.5 h-3.5 text-amber-600 shrink-0" />
                <span className="font-semibold">{profile.ventures[0].name}</span>
                <span className="text-slate-500 text-[11px]">— {profile.ventures[0].role}</span>
              </div>
            )}
          </div>
        </div>

        {/* BOTTOM: Heavy Action Button */}
        <div className="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-3">
          <div className="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
            <ShieldCheck className="w-4 h-4 text-emerald-600" />
            <span>Verified Credentials</span>
          </div>

          <button
            onClick={onOpenDossier}
            className="group/btn px-5 py-2.5 rounded-xl bg-navy hover:bg-cyan text-white text-xs font-display font-bold uppercase tracking-wider shadow-md hover:shadow-cyan-glow transition-all duration-300 flex items-center gap-2"
          >
            <span>View Full Credentials</span>
            <ArrowRight className="w-3.5 h-3.5 group-hover/btn:translate-x-1 transition-transform" />
          </button>
        </div>
      </div>
    </motion.div>
  );
};

/* -------------------------------------------------------------------------- */
/* FULL EXECUTIVE DOSSIER MODAL                                              */
/* -------------------------------------------------------------------------- */
interface ExecutiveDossierModalProps {
  profile: LeadershipProfile;
  onClose: () => void;
  onConsult: () => void;
}

const ExecutiveDossierModal: React.FC<ExecutiveDossierModalProps> = ({
  profile,
  onClose,
  onConsult
}) => {
  const getImageStyling = () => {
    switch (profile.id) {
      case 'zahid-bajwa':
        return 'object-cover object-[50%_25%] scale-[1.18]';
      case 'muzaffar-hussain':
        return 'object-cover object-[50%_28%] scale-[1.18]';
      case 'suhaib-cheema':
        return 'object-cover object-[52%_15%] scale-[1.95] origin-[52%_15%]';
      case 'muhammad-nawaz':
        return 'object-cover object-[29%_16%] scale-[1.85] origin-[29%_16%]';
      default:
        return 'object-cover object-top';
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
      {/* Backdrop */}
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
        onClick={onClose}
        className="fixed inset-0 bg-[#040E1B]/85 backdrop-blur-xl"
      />

      {/* Modal Dialog Card */}
      <motion.div
        initial={{ opacity: 0, scale: 0.9, y: 25 }}
        animate={{ opacity: 1, scale: 1, y: 0 }}
        exit={{ opacity: 0, scale: 0.9, y: 25 }}
        transition={{ type: 'spring', damping: 25, stiffness: 300 }}
        className="relative w-full max-w-4xl bg-gradient-to-b from-[#0B2545] via-[#07172C] to-[#040E1B] border-2 border-cyan/40 rounded-3xl p-6 sm:p-10 shadow-[0_25px_80px_rgba(0,0,0,0.85)] text-white z-10 max-h-[90vh] overflow-y-auto"
      >
        {/* Close Button */}
        <button
          onClick={onClose}
          className="absolute top-6 right-6 p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors z-20"
        >
          <X className="w-5 h-5" />
        </button>

        {/* Modal Content Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          {/* Left Column: Pill Arch Portrait & Meta */}
          <div className="lg:col-span-5 flex flex-col items-center text-center">
            <div className="relative w-48 sm:w-56 aspect-[1/1.45] rounded-t-full rounded-b-full overflow-hidden border-4 border-cyan/60 shadow-[0_0_35px_rgba(0,153,255,0.35)] bg-navy-dark mb-4">
              <img
                src={profile.image}
                alt={profile.name}
                className={`w-full h-full filter brightness-105 contrast-105 ${getImageStyling()}`}
              />
              <div className="absolute inset-0 bg-gradient-to-t from-navy/60 via-transparent to-transparent pointer-events-none" />
              <div className="absolute bottom-3 left-1/2 -translate-x-1/2 py-1 px-3 rounded-full bg-navy-deep/90 border border-white/20 text-xs font-semibold text-cyan-light flex items-center gap-1.5 whitespace-nowrap shadow-md">
                <ShieldCheck className="w-3.5 h-3.5 text-cyan" />
                <span>Verified Directorate</span>
              </div>
            </div>

            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-display font-semibold mb-2">
              <MapPin className="w-3.5 h-3.5 text-cyan" />
              <span>
                {profile.nationality} {profile.countryFlagEmoji}
              </span>
            </div>

            <p className="text-xs text-slate-400 font-medium">{profile.roleBadge}</p>

            <button
              onClick={onConsult}
              className="mt-6 w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-cyan via-blue-500 to-cyan bg-[length:200%_auto] hover:bg-right font-display text-xs sm:text-sm font-bold text-white shadow-[0_0_25px_rgba(0,153,255,0.4)] hover:shadow-[0_0_40px_rgba(0,153,255,0.6)] transition-all flex items-center justify-center gap-2"
            >
              <Sparkles className="w-4 h-4" />
              <span>Request Advisory Session</span>
            </button>
          </div>

          {/* Right Column: Full Academic & Industry Credentials */}
          <div className="lg:col-span-7 space-y-6">
            <div>
              <div className="inline-block text-xs font-display font-bold text-cyan uppercase tracking-wider mb-1">
                {profile.title} Credentials Dossier
              </div>
              <h2 className="font-display text-2xl sm:text-3xl font-extrabold text-white">
                {profile.name}
              </h2>
            </div>

            {/* Quote Box */}
            {profile.quote && (
              <div className="p-4 rounded-2xl bg-cyan/10 border-l-4 border-cyan text-slate-200 text-xs sm:text-sm italic leading-relaxed">
                "{profile.quote}"
              </div>
            )}

            {/* Verified Qualifications Section */}
            <div>
              <h4 className="font-display text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 mb-3">
                <GraduationCap className="w-4 h-4 text-cyan" />
                <span>Academic Degrees & International Fellowships</span>
              </h4>
              <div className="space-y-2.5">
                {profile.qualifications.map((qual, idx) => (
                  <div
                    key={idx}
                    className="p-3.5 rounded-xl bg-white/5 border border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2"
                  >
                    <div>
                      <div className="font-display font-bold text-sm text-white flex items-center gap-2">
                        <span className="px-2 py-0.5 rounded bg-cyan/20 text-cyan text-xs">
                          {qual.degree}
                        </span>
                        <span>{qual.institution}</span>
                      </div>
                      {qual.field && (
                        <div className="text-xs text-slate-300 mt-0.5">{qual.field}</div>
                      )}
                    </div>
                    {qual.country && (
                      <span className="text-xs text-slate-400 font-medium shrink-0">
                        {qual.country}
                      </span>
                    )}
                  </div>
                ))}
              </div>
            </div>

            {/* Commercial Ventures (if present) */}
            {profile.ventures && profile.ventures.length > 0 && (
              <div>
                <h4 className="font-display text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 mb-3">
                  <Building2 className="w-4 h-4 text-amber-400" />
                  <span>Commercial Ventures & Enterprise Leadership</span>
                </h4>
                <div className="space-y-2.5">
                  {profile.ventures.map((venture, vIdx) => (
                    <div
                      key={vIdx}
                      className="p-3.5 rounded-xl bg-amber-500/[0.07] border border-amber-500/20"
                    >
                      <div className="flex items-center justify-between gap-2 mb-1">
                        <span className="font-display font-bold text-sm text-amber-300">
                          {venture.name}
                        </span>
                        {venture.url && (
                          <a
                            href={venture.url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1 text-[11px] text-cyan hover:underline"
                          >
                            <span>{venture.url.replace(/^https?:\/\//, '')}</span>
                            <ExternalLink className="w-3 h-3" />
                          </a>
                        )}
                      </div>
                      <div className="text-xs text-slate-300 font-medium mb-1">
                        Role: {venture.role}
                      </div>
                      <p className="text-xs text-slate-400 leading-relaxed">
                        {venture.description}
                      </p>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* Experience & Advisory Track Record */}
            <div>
              <h4 className="font-display text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 mb-3">
                <Briefcase className="w-4 h-4 text-cyan" />
                <span>Executive Experience & Global Mentorship</span>
              </h4>
              <ul className="space-y-2 text-xs sm:text-sm text-slate-300">
                {profile.experience.map((exp, eIdx) => (
                  <li key={eIdx} className="flex items-start gap-2">
                    <CheckCircle2 className="w-4 h-4 text-cyan shrink-0 mt-0.5" />
                    <span>{exp}</span>
                  </li>
                ))}
              </ul>
            </div>

            {/* Specialization Badges */}
            <div>
              <h4 className="font-display text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                Specialization Areas
              </h4>
              <div className="flex flex-wrap gap-2">
                {profile.specializations.map((spec, sIdx) => (
                  <span
                    key={sIdx}
                    className="px-2.5 py-1 rounded-lg bg-cyan/10 border border-cyan/30 text-cyan-light text-xs font-medium"
                  >
                    {spec}
                  </span>
                ))}
              </div>
            </div>
          </div>
        </div>
      </motion.div>
    </div>
  );
};
