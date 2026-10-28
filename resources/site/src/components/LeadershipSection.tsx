import { getContent, siteAsset, safeUrl, route, siteConfig } from '../content';
import React, { useState, useRef } from 'react';
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
  ChevronLeft,
  ChevronRight,
  X,
  Mail,
  Globe2,
  Award,
  Star
} from 'lucide-react';
import { LEADERSHIP_PROFILES } from '../data/leadershipData';
import { LeadershipProfile } from '../types';
import { DenmarkFlag, UKFlag } from './CountryFlags';

interface LeadershipSectionProps {
  onOpenConsultation?: (profile?: LeadershipProfile) => void;
}

export const LeadershipSection: React.FC<LeadershipSectionProps> = ({ onOpenConsultation }) => {
  const [activeDossier, setActiveDossier] = useState<LeadershipProfile | null>(null);
  const [selectedIndex, setSelectedIndex] = useState(0);

  // Exclude CEO since CEO has his own dedicated section above
  const directors = LEADERSHIP_PROFILES.filter(p => p.id !== 'muhammad-nawaz');
  const activeProfile = directors[selectedIndex] || directors[0];

  const nextDirector = () => {
    setSelectedIndex((prev) => (prev + 1) % directors.length);
  };

  const prevDirector = () => {
    setSelectedIndex((prev) => (prev - 1 + directors.length) % directors.length);
  };

  const getImageStyling = (id: string) => {
    switch (id) {
      case 'dr-zahid-bajwa':
      case 'zahid-bajwa':
        return 'object-cover object-[50%_25%] scale-[1.12]';
      case 'muzaffar-hussain':
        return 'object-cover object-[50%_28%] scale-[1.12]';
      case 'suhaib-cheema':
        return 'object-cover object-[52%_15%] scale-[1.8] origin-[52%_15%]';
      default:
        return 'object-cover object-top';
    }
  };

  const renderFlag = (profile: LeadershipProfile) => {
    if (profile.countryCode === 'DK') return <DenmarkFlag className="w-4 h-3 rounded-sm shadow-sm" />;
    if (profile.countryCode === 'GB') return <UKFlag className="w-4 h-3 rounded-sm shadow-sm" />;
    return <span className="text-xs">{profile.countryFlagEmoji}</span>;
  };

  return (
    <section
      className="py-12 md:py-16 bg-gradient-to-b from-[#07172C] via-[#0B2545] to-[#040E1B] relative overflow-hidden text-navy border-b border-cyan/20"
      id="leadership"
    >
      {/* Ambient Cyber / Holographic Backdrops */}
      <div className="absolute inset-0 pointer-events-none overflow-hidden">
        <div className="absolute top-1/4 -left-48 w-80 h-80 bg-cyan/15 rounded-full blur-[140px]" />
        <div className="absolute bottom-1/4 -right-48 w-80 h-80 bg-blue-600/15 rounded-full blur-[140px]" />
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-cyan/10 rounded-full blur-[180px]" />
      </div>

      <div className="max-w-5xl mx-auto px-4 sm:px-6 relative z-10">
        {/* Section Header */}
        <div className="text-center max-w-2xl mx-auto mb-7 text-white">
          <motion.div
            initial={{ opacity: 0, y: 12 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan/15 border border-cyan/40 text-cyan-light text-[11px] font-display font-bold uppercase tracking-widest shadow-[0_0_15px_rgba(0,153,255,0.25)] mb-2.5"
          >
            <Sparkles className="w-3 h-3 text-cyan" />
            <span>{getContent<string>("sections.leadership.copy.internationalDirectorateAdvisoryBoard", "International Directorate & Advisory Board")}</span>
          </motion.div>

          <motion.h2
            initial={{ opacity: 0, y: 12 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.08 }}
            className="font-display text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight mb-2"
          >
            {getContent<string>("sections.leadership.copy.executiveLeadershipDirectorateMessages", "Executive Leadership & Directorate Messages")}
            <span className="text-cyan">.</span>
          </motion.h2>

          <motion.p
            initial={{ opacity: 0, y: 12 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.15 }}
            className="text-slate-300 text-xs sm:text-sm leading-relaxed"
          >
            {getContent<string>("sections.leadership.copy.europeanDoctoralFellowsErasmusMundusScholars", "European doctoral fellows, Erasmus Mundus scholars, and British enterprise leaders providing personal mentorship into the world's most prestigious institutions.")}
          </motion.p>

          {/* 1. COMPACT DIRECTOR SELECTOR TABS */}
          <div className="flex flex-wrap items-center justify-center gap-2 sm:gap-2.5 mt-5">
            {directors.map((director, idx) => {
              const isActive = selectedIndex === idx;
              return (
                <button
                  key={director.id}
                  onClick={() => setSelectedIndex(idx)}
                  className={`flex items-center gap-2 px-3 sm:px-4 py-1.5 rounded-full font-display text-xs font-semibold transition-all duration-200 cursor-pointer shadow-sm ${
                    isActive
                      ? 'bg-gradient-to-r from-cyan via-blue-500 to-cyan text-white shadow-[0_0_18px_rgba(0,153,255,0.45)] ring-2 ring-white/50 scale-102'
                      : 'bg-white/10 hover:bg-white/20 text-slate-200 border border-white/15 hover:border-cyan/40 hover:text-white'
                  }`}
                >
                  <div className="w-5 h-5 rounded-full overflow-hidden border border-white/60 shrink-0 bg-navy">
                    <img
                      src={siteAsset(director.image)}
                      alt={director.name}
                      className={`w-full h-full ${getImageStyling(director.id)}`}
                      loading="lazy"
                    />
                  </div>
                  <span className="truncate">{director.name}</span>
                  <span className="text-[11px]">{director.countryFlagEmoji}</span>
                </button>
              );
            })}
          </div>
        </div>

        {/* 2. SEAMLESS SPLIT PRESENTATION: LEFT IMAGE + RIGHT KEYNOTE MESSAGE (NO CARD BOX) */}
        <AnimatePresence mode="wait">
          <motion.div
            key={activeProfile.id}
            initial={{ opacity: 0, y: 16 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -16 }}
            transition={{ duration: 0.35, ease: 'easeInOut' }}
            className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center"
          >
            {/* LEFT COLUMN: Modern Director Portrait with Ambient Glow & Badges */}
            <div className="lg:col-span-5 flex flex-col items-center lg:items-start text-center lg:text-left">
              <motion.div
                whileHover={{ scale: 1.02 }}
                transition={{ duration: 0.3 }}
                className="relative group w-full max-w-[340px] mx-auto lg:mx-0"
              >
                {/* Ambient Glow Orb */}
                <div className="absolute -inset-2 bg-gradient-to-tr from-cyan via-blue-600 to-amber-500 rounded-3xl opacity-30 group-hover:opacity-60 blur-xl transition-all duration-500" />

                {/* Portrait Container */}
                <div className="relative rounded-3xl p-2.5 bg-gradient-to-b from-white/20 via-white/5 to-transparent border border-white/20 backdrop-blur-xl shadow-[0_20px_50px_rgba(0,0,0,0.6)] overflow-hidden">
                  <div className="relative aspect-[4/4.5] rounded-2xl overflow-hidden bg-navy-dark">
                    <img
                      src={siteAsset(activeProfile.image)}
                      alt={activeProfile.name}
                      loading="lazy"
                      decoding="async"
                      className={`w-full h-full filter brightness-105 contrast-105 transition-transform duration-700 ease-out group-hover:scale-108 ${getImageStyling(activeProfile.id)}`}
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-[#040E1B] via-transparent to-black/20 pointer-events-none" />

                    {/* Floating Country Badge (Top Right) */}
                    <div className="absolute top-3 right-3 bg-black/70 backdrop-blur-md px-3 py-1 rounded-full border border-white/20 text-xs font-display font-extrabold text-white flex items-center gap-1.5 shadow-lg">
                      {renderFlag(activeProfile)}
                      <span>{activeProfile.nationality}</span>
                    </div>

                    {/* Verified Fellowship Badge (Top Left) */}
                    <div className="absolute top-3 left-3 bg-cyan/25 backdrop-blur-md px-2.5 py-1 rounded-full border border-cyan/40 text-[11px] font-display font-bold text-cyan-light flex items-center gap-1 shadow-lg">
                      <Sparkles className="w-3 h-3 text-cyan" />
                      <span>Verified Mentor</span>
                    </div>

                    {/* Integrated Photo Base Badge */}
                    <div className="absolute bottom-3 inset-x-3 p-3 rounded-xl bg-black/65 backdrop-blur-md border border-white/10">
                      <h4 className="font-display font-black text-base sm:text-lg text-white tracking-tight drop-shadow-xs">
                        {activeProfile.name}
                      </h4>
                      <div className="text-[11px] font-bold text-cyan uppercase tracking-wider flex items-center justify-center lg:justify-start gap-1.5 mt-0.5">
                        <span>{activeProfile.title}</span>
                        <span>•</span>
                        <span>{activeProfile.nationality}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </motion.div>

              {/* Feature Sub-badge */}
              {activeProfile.featuredBadge && (
                <div className="mt-3.5 w-full max-w-[340px] mx-auto lg:mx-0">
                  <span className="inline-flex items-center gap-1.5 text-xs font-medium text-slate-300 bg-white/[0.05] border border-white/10 px-3.5 py-1.5 rounded-full shadow-sm">
                    <Award className="w-3.5 h-3.5 text-cyan shrink-0" />
                    <span>{activeProfile.featuredBadge}</span>
                  </span>
                </div>
              )}
            </div>

            {/* RIGHT COLUMN: Keynote Message, Academic Honors & Action Bar */}
            <div className="lg:col-span-7 flex flex-col justify-center">
              {/* Top Badges & Heading */}
              <div className="flex items-center gap-2 mb-2">
                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan/15 border border-cyan/40 text-cyan-light text-xs font-display font-bold uppercase tracking-wider">
                  <Sparkles className="w-3.5 h-3.5 text-cyan" />
                  <span>{activeProfile.roleBadge || 'Executive Directorate'}</span>
                </span>
                <span className="inline-flex items-center gap-1.5 text-xs font-display font-semibold text-slate-400 uppercase tracking-wider">
                  <Quote className="w-3.5 h-3.5 text-[#E88E75]" />
                  <span>Keynote Message</span>
                </span>
              </div>

              <h3 className="font-display text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight leading-tight mb-2">
                Welcome Message From Directorate<span className="text-cyan">.</span>
              </h3>

              <div className="w-16 h-1 bg-gradient-to-r from-cyan via-blue-500 to-transparent rounded-full mb-4" />

              {/* Keynote Quote Card with Ambient Glow */}
              <motion.div
                whileHover={{ x: 3 }}
                className="relative p-5 sm:p-6 rounded-2xl bg-white/[0.04] hover:bg-white/[0.07] border border-white/10 hover:border-cyan/40 backdrop-blur-xl transition-all duration-300 shadow-xl group mb-4"
              >
                <Quote className="absolute top-4 right-4 w-12 h-12 text-cyan/10 group-hover:text-cyan/20 transition-colors pointer-events-none" />
                <p className="text-slate-200 text-sm sm:text-base leading-relaxed italic font-normal relative z-10">
                  &ldquo;{activeProfile.quote || activeProfile.experience[0]}&rdquo;
                </p>
              </motion.div>

              {/* Academic Credentials & Global Honors */}
              <div className="mb-5">
                <span className="text-xs font-display font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5 mb-2.5">
                  <GraduationCap className="w-4 h-4 text-cyan" />
                  <span>Academic Credentials & Global Honors:</span>
                </span>
                <div className="flex flex-wrap gap-2">
                  {activeProfile.qualifications.map((q, qIdx) => (
                    <span
                      key={qIdx}
                      className="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/[0.05] border border-white/10 text-slate-200 text-xs font-medium hover:border-cyan/40 hover:bg-cyan/10 transition-colors shadow-sm"
                    >
                      <CheckCircle2 className="w-3.5 h-3.5 text-cyan shrink-0" />
                      <span>
                        <strong className="text-white font-semibold">{q.degree}:</strong> {q.institution}
                      </span>
                    </span>
                  ))}
                </div>
              </div>

              {/* Action Buttons & Fast Director Navigation */}
              <div className="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-white/10">
                <div className="flex flex-wrap items-center gap-3">
                  <button
                    onClick={() => setActiveDossier(activeProfile)}
                    className="px-5 py-2.5 rounded-xl bg-cyan hover:bg-cyan-light text-navy text-xs sm:text-sm font-display font-bold uppercase tracking-wider shadow-[0_0_20px_rgba(0,153,255,0.4)] hover:shadow-[0_0_30px_rgba(0,153,255,0.6)] hover:-translate-y-0.5 active:scale-95 transition-all flex items-center gap-2 cursor-pointer"
                  >
                    <span>View Full Dossier</span>
                    <ArrowRight className="w-4 h-4" />
                  </button>

                  <button
                    onClick={() => onOpenConsultation?.(activeProfile)}
                    className="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#E88E75] via-[#DF7B62] to-[#E88E75] hover:brightness-110 text-white text-xs sm:text-sm font-display font-bold uppercase tracking-wider shadow-[0_4px_20px_rgba(232,142,117,0.35)] hover:shadow-[0_6px_25px_rgba(232,142,117,0.5)] hover:-translate-y-0.5 active:scale-95 transition-all flex items-center gap-2 cursor-pointer"
                  >
                    <Mail className="w-4 h-4" />
                    <span>Consult with Directorate</span>
                  </button>
                </div>

                {/* Previous / Next Director Cycle Controls */}
                <div className="flex items-center gap-2">
                  <span className="text-xs font-display font-bold text-slate-400 mr-1">
                    {selectedIndex + 1} / {directors.length}
                  </span>
                  <button
                    onClick={prevDirector}
                    aria-label="Previous Director"
                    className="w-9 h-9 rounded-full bg-white/10 hover:bg-cyan hover:text-navy border border-white/20 text-white flex items-center justify-center transition-all hover:scale-105 active:scale-95 cursor-pointer shadow-sm"
                  >
                    <ChevronLeft className="w-4 h-4" />
                  </button>
                  <button
                    onClick={nextDirector}
                    aria-label="Next Director"
                    className="w-9 h-9 rounded-full bg-white/10 hover:bg-cyan hover:text-navy border border-white/20 text-white flex items-center justify-center transition-all hover:scale-105 active:scale-95 cursor-pointer shadow-sm"
                  >
                    <ChevronRight className="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>
          </motion.div>
        </AnimatePresence>

        {/* Compact Section Bottom Banner */}
        <motion.div
          initial={{ opacity: 0, y: 15 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="mt-8 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-white/[0.06] via-cyan/[0.08] to-white/[0.04] border border-cyan/30 backdrop-blur-md relative overflow-hidden shadow-lg text-white"
        >
          <div className="flex flex-col sm:flex-row items-center justify-between gap-4 relative z-10">
            <div className="text-center sm:text-left">
              <span className="text-cyan text-[11px] font-display font-bold uppercase tracking-wider block">
                {getContent<string>("sections.leadership.copy.directMentorshipScholarshipAdvisory", "Direct Mentorship & Scholarship Advisory")}
              </span>
              <h3 className="font-display text-base sm:text-lg font-bold text-white mt-0.5">
                {getContent<string>("sections.leadership.copy.consultWithOurDirectorateForErasmus", "Consult with our Directorate for Erasmus Mundus & UK Visas")}
              </h3>
            </div>
            <button
              onClick={() => onOpenConsultation?.()}
              className="shrink-0 px-5 py-2.5 rounded-xl bg-gradient-to-r from-cyan via-blue-500 to-cyan font-display text-xs font-bold text-white shadow-[0_0_20px_rgba(0,153,255,0.4)] hover:shadow-[0_0_30px_rgba(0,153,255,0.6)] hover:scale-102 active:scale-95 transition-all flex items-center gap-2 cursor-pointer"
            >
              <Sparkles className="w-3.5 h-3.5 text-white" />
              <span>{getContent<string>("sections.leadership.copy.bookDirectorateAdvisory", "Book Directorate Advisory")}</span>
              <ArrowRight className="w-3.5 h-3.5" />
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
      case 'dr-zahid-bajwa':
      case 'zahid-bajwa':
        return 'object-cover object-[50%_25%] scale-[1.12]';
      case 'muzaffar-hussain':
        return 'object-cover object-[50%_28%] scale-[1.12]';
      case 'suhaib-cheema':
        return 'object-cover object-[52%_15%] scale-[1.8] origin-[52%_15%]';
      default:
        return 'object-cover object-top';
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
        onClick={onClose}
        className="fixed inset-0 bg-[#040E1B]/85 backdrop-blur-xl"
      />

      <motion.div
        initial={{ opacity: 0, scale: 0.9, y: 25 }}
        animate={{ opacity: 1, scale: 1, y: 0 }}
        exit={{ opacity: 0, scale: 0.9, y: 25 }}
        transition={{ type: 'spring', damping: 25, stiffness: 300 }}
        className="relative w-full max-w-4xl bg-gradient-to-b from-[#0F356B] via-[#0B2545] to-[#040E1B] border-2 border-cyan/40 rounded-3xl p-6 sm:p-10 shadow-[0_25px_80px_rgba(0,0,0,0.85)] text-white z-10 max-h-[90vh] overflow-y-auto"
      >
        <button
          onClick={onClose}
          className="absolute top-6 right-6 p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors z-20 cursor-pointer"
        >
          <X className="w-5 h-5" />
        </button>

        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          {/* Left Column: Headshot & Action */}
          <div className="lg:col-span-5 flex flex-col items-center text-center">
            <div className="relative w-44 h-44 sm:w-52 sm:h-52 rounded-full border-4 border-white shadow-[0_0_35px_rgba(0,153,255,0.4)] overflow-hidden bg-navy-dark mb-4">
              <img
                loading="lazy"
                decoding="async"
                src={siteAsset(profile.image)}
                alt={profile.name}
                className={`w-full h-full filter brightness-105 contrast-105 ${getImageStyling()}`}
              />
            </div>

            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-display font-semibold mb-2">
              <MapPin className="w-3.5 h-3.5 text-cyan" />
              <span>
                {profile.nationality} {profile.countryFlagEmoji}
              </span>
            </div>

            <p className="text-xs text-slate-300 font-medium">{profile.roleBadge}</p>
            {profile.featuredBadge && <p className="text-xs text-cyan-light mt-2">{profile.featuredBadge}</p>}

            <button
              onClick={onConsult}
              className="mt-6 w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-cyan via-blue-500 to-cyan bg-[length:200%_auto] hover:bg-right font-display text-xs sm:text-sm font-bold text-white shadow-[0_0_25px_rgba(0,153,255,0.4)] hover:shadow-[0_0_40px_rgba(0,153,255,0.6)] transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
              <Sparkles className="w-4 h-4" />
              <span>{getContent<string>("sections.leadership.copy.requestDirectorateAdvisory", "Request Directorate Advisory")}</span>
            </button>
          </div>

          {/* Right Column: Full Academic & Industry Credentials */}
          <div className="lg:col-span-7 space-y-6">
            <div>
              <div className="inline-block text-xs font-display font-bold text-cyan uppercase tracking-wider mb-1">
                {getContent<string>("sections.leadership.copy.directorDossier", "Director Dossier")}
              </div>
              <h2 className="font-display text-2xl sm:text-3xl font-extrabold text-white">
                {profile.name}
              </h2>
            </div>

            {profile.quote && (
              <div className="p-4 rounded-2xl bg-cyan/15 border-l-4 border-cyan text-slate-200 text-xs sm:text-sm italic leading-relaxed">
                &ldquo;{profile.quote}&rdquo;
              </div>
            )}

            <div>
              <h4 className="font-display text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 mb-3">
                <GraduationCap className="w-4 h-4 text-cyan" />
                <span>{getContent<string>("sections.leadership.copy.academicDegreesInternationalFellowships", "Academic Degrees & International Fellowships")}</span>
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

            {profile.ventures && profile.ventures.length > 0 && (
              <div>
                <h4 className="font-display text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 mb-3">
                  <Building2 className="w-4 h-4 text-amber-400" />
                  <span>{getContent<string>("sections.leadership.copy.commercialVenturesEnterpriseLeadership", "Commercial Ventures & Enterprise Leadership")}</span>
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
                            href={safeUrl(venture.url || '#')}
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
                        {getContent<string>("sections.leadership.copy.role", "Role: ")}{venture.role}
                      </div>
                      <p className="text-xs text-slate-400 leading-relaxed">
                        {venture.description}
                      </p>
                    </div>
                  ))}
                </div>
              </div>
            )}

            <div>
              <h4 className="font-display text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 mb-3">
                <Briefcase className="w-4 h-4 text-cyan" />
                <span>{getContent<string>("sections.leadership.copy.executiveMentorshipTrackRecord", "Executive Mentorship & Track Record")}</span>
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

            {!!profile.achievements?.length && (
              <div>
                <h4 className="font-display text-sm font-bold uppercase tracking-wider text-slate-300 mb-3">
                  {getContent('sections.leadership.copy.achievements', 'Achievements')}
                </h4>
                <ul className="space-y-2 text-xs text-slate-300">
                  {profile.achievements.map((achievement, index) => (
                    <li key={index} className="flex gap-2">
                      <CheckCircle2 className="w-4 h-4 text-cyan shrink-0" />
                      <span>{achievement}</span>
                    </li>
                  ))}
                </ul>
              </div>
            )}

            <div>
              <h4 className="font-display text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                {getContent<string>("sections.leadership.copy.specializationAreas", "Specialization Areas")}
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
