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
  Mail,
  Globe2
} from 'lucide-react';
import { LEADERSHIP_PROFILES } from '../data/leadershipData';
import { LeadershipProfile } from '../types';
import { DenmarkFlag, UKFlag } from './CountryFlags';

interface LeadershipSectionProps {
  onOpenConsultation?: (profile?: LeadershipProfile) => void;
}

export const LeadershipSection: React.FC<LeadershipSectionProps> = ({ onOpenConsultation }) => {
  const [activeDossier, setActiveDossier] = useState<LeadershipProfile | null>(null);

  // Exclude CEO since CEO has his own dedicated section above
  const directors = LEADERSHIP_PROFILES.filter((p) => p.title.toLowerCase().includes('director'));

  return (
    <section
      className="py-24 md:py-36 bg-gradient-to-b from-[#07172C] via-[#0B2545] to-[#040E1B] relative overflow-hidden text-navy border-b border-cyan/20"
      id="leadership"
    >
      {/* Ambient Logo Theme Backdrops */}
      <div className="absolute inset-0 pointer-events-none overflow-hidden">
        <div className="absolute top-1/4 -left-48 w-[40rem] h-[40rem] bg-cyan/15 rounded-full blur-[180px] animate-pulse" />
        <div className="absolute bottom-1/4 -right-48 w-[40rem] h-[40rem] bg-blue-600/15 rounded-full blur-[180px]" />
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[50rem] h-[50rem] bg-cyan/10 rounded-full blur-[220px]" />
      </div>

      <div className="max-w-6xl mx-auto px-6 relative z-10">
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-20 text-white">
          <motion.div
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/15 border border-cyan/40 text-cyan-light text-xs font-display font-bold uppercase tracking-widest shadow-[0_0_20px_rgba(0,153,255,0.35)] mb-4"
          >
            <Sparkles className="w-3.5 h-3.5 text-cyan animate-spin-slow" />
            <span>International Directorate & Advisory Board</span>
          </motion.div>

          <motion.h2
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.1 }}
            className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-[1.18] mb-5"
          >
            Executive Leadership & Directorate Messages<span className="heading-cursor-line">|</span>
          </motion.h2>

          <motion.p
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.2 }}
            className="text-slate-300 text-sm sm:text-base leading-relaxed"
          >
            European doctoral scholars, scholarship chairs, and British enterprise founders guiding our scholars directly into world powerhouses.
          </motion.p>
        </div>

        {/* ALTERNATING ZIG-ZAG CARDS (Left -> Right -> Left) */}
        <div className="space-y-20 lg:space-y-28">
          {directors.map((profile, idx) => {
            const isRightAligned = idx % 2 === 1;

            return (
              <div
                key={profile.id}
                className={`flex w-full ${isRightAligned ? 'justify-end' : 'justify-start'}`}
              >
                <div className="w-full lg:w-[86%] xl:w-[82%]">
                  <AinexPosterCard
                    profile={profile}
                    index={idx}
                    isRightAligned={isRightAligned}
                    onOpenDossier={() => setActiveDossier(profile)}
                    onOpenConsultation={() => onOpenConsultation?.(profile)}
                  />
                </div>
              </div>
            );
          })}
        </div>

        {/* Section Bottom Banner */}
        <motion.div
          initial={{ opacity: 0, y: 25 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="mt-24 p-8 rounded-3xl bg-gradient-to-r from-white/[0.06] via-cyan/[0.08] to-white/[0.04] border border-cyan/30 backdrop-blur-xl relative overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)] text-white"
        >
          <div className="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
            <div className="text-center md:text-left">
              <span className="text-cyan text-xs font-display font-bold uppercase tracking-wider block mb-1">
                Direct Mentorship & Scholarship Advisory
              </span>
              <h3 className="font-display text-2xl font-extrabold text-white">
                Consult with our Directorate for Erasmus Mundus & UK Visas
              </h3>
              <p className="text-slate-300 text-sm mt-1">
                Personalized university applications, Scandinavian scholarship guidance, and British enterprise mentorship.
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
/* AINEX / LARGE HEROIC PORTRAIT POSTER CARD (Exact Reference Replication)     */
/* -------------------------------------------------------------------------- */
interface AinexPosterCardProps {
  profile: LeadershipProfile;
  index: number;
  isRightAligned: boolean;
  onOpenDossier: () => void;
  onOpenConsultation: () => void;
}

const AinexPosterCard: React.FC<AinexPosterCardProps> = ({
  profile,
  index,
  isRightAligned,
  onOpenDossier,
  onOpenConsultation
}) => {
  // Custom zoom & focal position for each photo to keep face huge and prominent
  const getImageStyling = () => {
    switch (profile.id) {
      case 'zahid-bajwa':
        return 'object-cover object-[50%_22%] scale-[1.08]';
      case 'muzaffar-hussain':
        return 'object-cover object-[50%_25%] scale-[1.08]';
      case 'suhaib-cheema':
        return 'object-cover object-[52%_15%] scale-[1.75] origin-[52%_15%]';
      default:
        return 'object-cover object-top';
    }
  };

  const renderFlag = () => {
    if (profile.countryCode === 'DK') return <DenmarkFlag className="w-5 h-3.5 rounded-sm shadow-sm" />;
    if (profile.countryCode === 'GB') return <UKFlag className="w-5 h-3.5 rounded-sm shadow-sm" />;
    return <span className="text-sm">{profile.countryFlagEmoji}</span>;
  };

  const getWebsiteUrl = () => {
    if (profile.id === 'muzaffar-hussain') return 'www.donerking.dk';
    if (profile.id === 'suhaib-cheema') return 'www.horizon-foods.co.uk';
    return 'www.genzinternational.com';
  };

  const getWebsiteHref = () => {
    if (profile.id === 'muzaffar-hussain') return 'https://www.donerking.dk';
    if (profile.id === 'suhaib-cheema') return 'https://www.horizon-foods.co.uk';
    return '#';
  };

  return (
    <motion.div
      initial={{ opacity: 0, x: isRightAligned ? 40 : -40 }}
      whileInView={{ opacity: 1, x: 0 }}
      viewport={{ once: true, margin: '-50px' }}
      transition={{ duration: 0.6, delay: 0.1 }}
      className="relative group w-full"
    >
      {/* Outer Card Glow */}
      <div className="absolute -inset-1 bg-gradient-to-r from-cyan/30 via-blue-500/20 to-cyan/30 rounded-[3rem] opacity-50 group-hover:opacity-100 blur-xl transition-all duration-500 pointer-events-none" />

      {/* Main White / Light Card Container (Recreation of Reference Poster) */}
      <div className="relative rounded-[2.5rem] sm:rounded-[3rem] bg-white text-navy p-6 sm:p-10 md:p-12 shadow-[0_25px_70px_rgba(0,0,0,0.6)] border-4 border-slate-100 overflow-hidden transition-all duration-300 group-hover:border-cyan/40">
        {/* Hexagon Honeycomb Background Pattern (Exact Reference Detail) */}
        <div
          className="absolute inset-0 opacity-[0.04] pointer-events-none"
          style={{
            backgroundImage: `url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='49' viewBox='0 0 28 49'%3E%3Cpath fill-rule='evenodd' stroke='%230B2545' stroke-width='1' fill='none' d='M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM0 0h28v49H0z'/%3E%3C/svg%3E")`,
            backgroundSize: '56px 98px'
          }}
        />

        {/* Diagonal Accent Slashes (Exact Reference Detail) */}
        <div className="absolute -right-16 top-1/2 w-72 h-14 bg-gradient-to-r from-cyan via-blue-600 to-transparent -rotate-25 rounded-full opacity-80 pointer-events-none" />
        <div className="absolute -left-16 bottom-1/4 w-60 h-10 bg-slate-100 -rotate-25 rounded-full opacity-60 pointer-events-none" />

        {/* 1. TOP BRAND HEADER (Exact Reference Placement) */}
        <div className="relative z-10 flex flex-col items-center mb-6">
          <div className="flex items-center gap-2 mb-4">
            <span className="font-display font-black text-2xl sm:text-3xl text-navy tracking-tight">
              GEN<span className="text-cyan">Z</span>
            </span>
            <div className="h-4 w-[1.5px] bg-slate-300" />
            <span className="text-xs uppercase font-display font-bold tracking-widest text-slate-500">
              International
            </span>
          </div>

          {/* Dark Navy Statement Banner (Exact Reference Placement) */}
          <div className="w-full max-w-2xl px-6 py-3.5 rounded-2xl bg-[#07172C] text-white text-center shadow-lg border border-cyan/20">
            <p className="text-xs sm:text-sm font-medium leading-relaxed">
              At <strong className="text-cyan-light font-bold">Gen Z International</strong>, we don’t just teach curriculum — we architect prestigious global scholarships, British careers, and lifelong international leadership.
            </p>
          </div>
        </div>

        {/* 2. THE LARGE HEROIC CIRCULAR PORTRAIT (Centerpiece of the Design) */}
        <div className="relative z-10 flex justify-center my-6 sm:my-8">
          <div className="relative">
            {/* Deep Navy Semi-Circle Backdrop (Exact Reference Arch Shape) */}
            <div className="absolute -inset-6 sm:-inset-10 bg-[#0B2545] rounded-t-full rounded-b-none opacity-95 shadow-2xl" />

            {/* Glowing Accent Arc Ring */}
            <div className="absolute -inset-7 sm:-inset-11 border-4 border-cyan/40 rounded-t-full pointer-events-none" />

            {/* Massive Circular Portrait Container (Huge, Clear, Sharp!) */}
            <div className="relative w-64 h-64 sm:w-80 sm:h-80 md:w-96 md:h-96 rounded-full border-8 border-white shadow-[0_15px_40px_rgba(11,37,69,0.35)] overflow-hidden bg-navy-dark transition-transform duration-500 group-hover:scale-[1.02]">
              <img
                src={profile.image}
                alt={profile.name}
                className={`w-full h-full filter brightness-105 contrast-105 transition-transform duration-700 ${getImageStyling()}`}
              />

              {/* Bottom Subtle Gradient */}
              <div className="absolute inset-0 bg-gradient-to-t from-navy-dark/40 via-transparent to-transparent pointer-events-none" />
            </div>

            {/* Floating Nationality Badge at Portrait Base */}
            <div className="absolute -bottom-3 left-1/2 -translate-x-1/2 inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white shadow-xl border-2 border-slate-200 text-navy text-xs font-display font-bold">
              {renderFlag()}
              <span>{profile.nationality}</span>
            </div>
          </div>
        </div>

        {/* 3. NAME, ROLE & FRAMED QUOTATION */}
        <div className="relative z-10 text-center max-w-2xl mx-auto mt-6 mb-6">
          <h3 className="font-display text-3xl sm:text-4xl font-black text-navy tracking-tight mb-1">
            {profile.name}
          </h3>

          <p className="text-sm sm:text-base font-semibold text-slate-500 mb-6">
            {profile.title} · {profile.roleBadge}
          </p>

          {/* Quotation Area Framed with Big Quotes (Exact Reference Detail) */}
          <div className="relative px-8 sm:px-14 py-4 group-hover:bg-slate-50/80 rounded-2xl transition-colors duration-300">
            {/* Big Solid Navy Opening Quote on Left */}
            <div className="absolute left-0 top-0 text-navy select-none">
              <Quote className="w-8 h-8 sm:w-11 sm:h-11 text-navy fill-navy" />
            </div>

            {/* Authentic Quote Text */}
            <p className="text-slate-700 text-sm sm:text-base md:text-lg leading-relaxed font-normal italic">
              "{profile.quote || profile.experience[0]}"
            </p>

            {/* Big Solid Navy Closing Quote on Right */}
            <div className="absolute right-0 bottom-0 text-navy select-none">
              <Quote className="w-8 h-8 sm:w-11 sm:h-11 text-navy fill-navy rotate-180" />
            </div>
          </div>

          {/* Academic Degrees & Honors Badges */}
          <div className="flex flex-wrap items-center justify-center gap-2 mt-6">
            {profile.qualifications.map((q, qIdx) => (
              <span
                key={qIdx}
                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-navy text-xs font-semibold"
              >
                <CheckCircle2 className="w-3.5 h-3.5 text-cyan shrink-0" />
                <span>
                  <strong>{q.degree}:</strong> {q.institution}
                </span>
              </span>
            ))}
          </div>
        </div>

        {/* 4. BOTTOM PILL ACTION BUTTONS (Exact Reference Detail) */}
        <div className="relative z-10 pt-6 border-t-2 border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
          {/* Left Pill: Direct Advisory Button */}
          <button
            onClick={onOpenConsultation}
            className="w-full sm:w-auto px-6 py-3 rounded-full bg-[#07172C] hover:bg-cyan text-white text-xs sm:text-sm font-display font-bold shadow-md hover:shadow-cyan-glow transition-all duration-300 flex items-center justify-center gap-2 group/btn"
          >
            <Mail className="w-4 h-4 text-cyan group-hover/btn:text-white" />
            <span>Advisory & Mentorship Office</span>
          </button>

          {/* Center: Inspect Full Dossier */}
          <button
            onClick={onOpenDossier}
            className="text-xs sm:text-sm font-display font-bold text-navy hover:text-cyan transition-colors underline underline-offset-4 flex items-center gap-1"
          >
            <span>View Complete Credentials Dossier</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </button>

          {/* Right Pill: Website Link */}
          {profile.ventures && profile.ventures.length > 0 ? (
            <a
              href={getWebsiteHref()}
              target="_blank"
              rel="noopener noreferrer"
              className="w-full sm:w-auto px-6 py-3 rounded-full bg-[#07172C] hover:bg-cyan text-white text-xs sm:text-sm font-display font-bold shadow-md hover:shadow-cyan-glow transition-all duration-300 flex items-center justify-center gap-2 group/link"
            >
              <Globe2 className="w-4 h-4 text-cyan group-hover/link:text-white" />
              <span>{getWebsiteUrl()}</span>
              <ExternalLink className="w-3 h-3 ml-1" />
            </a>
          ) : (
            <div className="w-full sm:w-auto px-6 py-3 rounded-full bg-[#07172C] text-white text-xs sm:text-sm font-display font-bold shadow-md flex items-center justify-center gap-2">
              <Globe2 className="w-4 h-4 text-cyan" />
              <span>{getWebsiteUrl()}</span>
            </div>
          )}
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
        return 'object-cover object-[50%_22%] scale-[1.08]';
      case 'muzaffar-hussain':
        return 'object-cover object-[50%_25%] scale-[1.08]';
      case 'suhaib-cheema':
        return 'object-cover object-[52%_15%] scale-[1.75] origin-[52%_15%]';
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
          className="absolute top-6 right-6 p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors z-20"
        >
          <X className="w-5 h-5" />
        </button>

        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          {/* Left Column: Big Circular Headshot & Action */}
          <div className="lg:col-span-5 flex flex-col items-center text-center">
            <div className="relative w-44 h-44 sm:w-52 sm:h-52 rounded-full border-4 border-white shadow-[0_0_35px_rgba(0,153,255,0.4)] overflow-hidden bg-navy-dark mb-4">
              <img
                src={profile.image}
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

            <button
              onClick={onConsult}
              className="mt-6 w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-cyan via-blue-500 to-cyan bg-[length:200%_auto] hover:bg-right font-display text-xs sm:text-sm font-bold text-white shadow-[0_0_25px_rgba(0,153,255,0.4)] hover:shadow-[0_0_40px_rgba(0,153,255,0.6)] transition-all flex items-center justify-center gap-2"
            >
              <Sparkles className="w-4 h-4" />
              <span>Request Directorate Advisory</span>
            </button>
          </div>

          {/* Right Column: Full Academic & Industry Credentials */}
          <div className="lg:col-span-7 space-y-6">
            <div>
              <div className="inline-block text-xs font-display font-bold text-cyan uppercase tracking-wider mb-1">
                Director Dossier
              </div>
              <h2 className="font-display text-2xl sm:text-3xl font-extrabold text-white">
                {profile.name}
              </h2>
            </div>

            {profile.quote && (
              <div className="p-4 rounded-2xl bg-cyan/15 border-l-4 border-cyan text-slate-200 text-xs sm:text-sm italic leading-relaxed">
                "{profile.quote}"
              </div>
            )}

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

            <div>
              <h4 className="font-display text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 mb-3">
                <Briefcase className="w-4 h-4 text-cyan" />
                <span>Executive Mentorship & Track Record</span>
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
