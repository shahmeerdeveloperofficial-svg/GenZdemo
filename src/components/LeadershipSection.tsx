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
  Star,
  Globe2,
  Award
} from 'lucide-react';
import { LEADERSHIP_PROFILES } from '../data/leadershipData';
import { LeadershipProfile } from '../types';
import { DenmarkFlag, UKFlag } from './CountryFlags';

interface LeadershipSectionProps {
  onOpenConsultation?: (profile?: LeadershipProfile) => void;
}

export const LeadershipSection: React.FC<LeadershipSectionProps> = ({ onOpenConsultation }) => {
  const [activeDossier, setActiveDossier] = useState<LeadershipProfile | null>(null);
  const [hoveredCardId, setHoveredCardId] = useState<string | null>(null);

  // Filter out the CEO since the CEO has his dedicated section above
  const directors = LEADERSHIP_PROFILES.filter((p) => p.title.toLowerCase().includes('director'));

  return (
    <section
      className="py-24 md:py-36 bg-gradient-to-b from-[#07172C] via-[#0B2545] to-[#040E1B] relative overflow-hidden text-white border-b border-cyan/20"
      id="leadership"
    >
      {/* Ambient Logo Theme Cyber Backdrops & Dot Matrix (As in Reference Poster) */}
      <div className="absolute inset-0 pointer-events-none overflow-hidden">
        {/* Top Left Cyan Glow */}
        <div className="absolute top-1/4 -left-48 w-[40rem] h-[40rem] bg-cyan/15 rounded-full blur-[180px] animate-pulse" />
        {/* Bottom Right Blue Orb */}
        <div className="absolute bottom-1/4 -right-48 w-[40rem] h-[40rem] bg-blue-600/15 rounded-full blur-[180px]" />
        {/* Center Glow */}
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[45rem] h-[45rem] bg-cyan/10 rounded-full blur-[200px]" />

        {/* Dot Matrix Pattern (Bottom Right - Reference Image Signature) */}
        <div
          className="absolute bottom-10 right-10 w-96 h-96 opacity-25"
          style={{
            backgroundImage: `radial-gradient(circle, #0099FF 1.5px, transparent 1.5px)`,
            backgroundSize: '20px 20px'
          }}
        />

        {/* Dot Matrix Pattern (Top Left) */}
        <div
          className="absolute top-10 left-10 w-72 h-72 opacity-20"
          style={{
            backgroundImage: `radial-gradient(circle, #38BDF8 1.5px, transparent 1.5px)`,
            backgroundSize: '24px 24px'
          }}
        />

        {/* Elegant Curved Circuit Lines (Alternating Connecting Path) */}
        <svg
          className="absolute inset-0 w-full h-full opacity-15 hidden lg:block"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M 250,200 Q 600,450 950,700 T 300,1200"
            fill="none"
            stroke="#0099FF"
            strokeWidth="1.5"
            strokeDasharray="8 8"
          />
        </svg>
      </div>

      <div className="max-w-6xl mx-auto px-6 relative z-10">
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-20">
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
            European & British Executive Directors<span className="heading-cursor-line">|</span>
          </motion.h2>

          <motion.p
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.2 }}
            className="text-slate-300 text-sm sm:text-base leading-relaxed"
          >
            Direct guidance from European doctoral scholars, British enterprise leaders, and scholarship chairs from Aarhus, Copenhagen, Bangor, and London — forging uncompromised international pathways for our scholars.
          </motion.p>
        </div>

        {/* ALTERNATING ZIG-ZAG CARDS (Left -> Right -> Left) */}
        <div className="space-y-16 lg:space-y-24">
          {directors.map((profile, idx) => {
            // Alternating alignment: Even index = Left, Odd index = Right
            const isRightAligned = idx % 2 === 1;

            return (
              <div
                key={profile.id}
                className={`flex w-full ${isRightAligned ? 'justify-end' : 'justify-start'}`}
              >
                <div className="w-full lg:w-[85%] xl:w-[78%]">
                  <DevntomStyleCard
                    profile={profile}
                    index={idx}
                    isRightAligned={isRightAligned}
                    isHovered={hoveredCardId === profile.id}
                    onHover={() => setHoveredCardId(profile.id)}
                    onLeave={() => setHoveredCardId(null)}
                    onOpenDossier={() => setActiveDossier(profile)}
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
          className="mt-20 p-8 rounded-3xl bg-gradient-to-r from-white/[0.06] via-cyan/[0.08] to-white/[0.04] border border-cyan/30 backdrop-blur-xl relative overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)]"
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
/* DEVNTOM STYLE EXECUTIVE CARD (Recreation of Reference Poster Image)        */
/* -------------------------------------------------------------------------- */
interface DevntomStyleCardProps {
  profile: LeadershipProfile;
  index: number;
  isRightAligned: boolean;
  isHovered: boolean;
  onHover: () => void;
  onLeave: () => void;
  onOpenDossier: () => void;
}

const DevntomStyleCard: React.FC<DevntomStyleCardProps> = ({
  profile,
  index,
  isRightAligned,
  isHovered,
  onHover,
  onLeave,
  onOpenDossier
}) => {
  // Custom zoom & focal position for each photo
  const getImageStyling = () => {
    switch (profile.id) {
      case 'zahid-bajwa':
        return 'object-cover object-[50%_25%] scale-[1.2]';
      case 'muzaffar-hussain':
        return 'object-cover object-[50%_28%] scale-[1.2]';
      case 'suhaib-cheema':
        return 'object-cover object-[52%_15%] scale-[2.1] origin-[52%_15%]';
      default:
        return 'object-cover object-top';
    }
  };

  const renderFlag = () => {
    if (profile.countryCode === 'DK') return <DenmarkFlag className="w-4 h-3 rounded-sm shadow-sm" />;
    if (profile.countryCode === 'GB') return <UKFlag className="w-4 h-3 rounded-sm shadow-sm" />;
    return <span className="text-xs">{profile.countryFlagEmoji}</span>;
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
      onMouseEnter={onHover}
      onMouseLeave={onLeave}
      className="relative group w-full"
    >
      {/* Top Organization Header outside Card (Just like Devntom Solutions in Poster) */}
      <div
        className={`flex items-center gap-2 mb-3 px-2 ${
          isRightAligned ? 'justify-end' : 'justify-start'
        }`}
      >
        <div className="flex items-center gap-2 text-cyan font-display font-extrabold text-xs sm:text-sm tracking-wider uppercase">
          <Globe2 className="w-4 h-4 text-cyan" />
          <span>Gen Z International Directorate</span>
        </div>
      </div>

      {/* Main Card Container (Exact Recreation of Devntom Reference Shape & Look) */}
      <div
        className={`relative rounded-[2.5rem] bg-gradient-to-br from-[#0F356B] via-[#0B2A58] to-[#071E40] border-2 p-7 sm:p-10 shadow-[0_25px_60px_rgba(0,0,0,0.6)] transition-all duration-500 overflow-hidden ${
          isHovered
            ? 'border-cyan shadow-[0_30px_80px_rgba(0,153,255,0.4)] -translate-y-1.5'
            : 'border-cyan/40 hover:border-cyan/70'
        }`}
      >
        {/* Soft Background Radial Light inside Card */}
        <div className="absolute top-0 right-1/4 w-80 h-80 bg-cyan/15 rounded-full blur-[100px] pointer-events-none" />

        {/* TOP ROW: Large Opening Quote on Left + Name/Title + Circular Portrait on Right */}
        <div className="flex flex-col-reverse sm:flex-row sm:items-start justify-between gap-6 relative z-10 mb-6">
          {/* Left: Large Solid Opening Quote + Name & Title */}
          <div className="flex items-start gap-4 flex-1">
            {/* Solid Cyan Opening Quote Icon (Exact Devntom Iconography) */}
            <div
              className={`shrink-0 w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-cyan/20 border border-cyan/40 flex items-center justify-center text-cyan shadow-[0_0_20px_rgba(0,153,255,0.3)] transition-transform duration-300 ${
                isHovered ? 'scale-110 shadow-[0_0_30px_rgba(0,153,255,0.6)]' : ''
              }`}
            >
              <Quote className="w-8 h-8 sm:w-9 sm:h-9 text-cyan fill-cyan" />
            </div>

            {/* Name & Title */}
            <div>
              <div className="flex items-center gap-2 flex-wrap mb-1">
                <h3 className="font-display text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                  {profile.name}
                </h3>
              </div>

              <div className="flex items-center gap-2 text-cyan-light text-xs sm:text-sm font-semibold mb-1">
                <span>{profile.title}, Gen Z International</span>
                <span>•</span>
                <span className="inline-flex items-center gap-1 text-white">
                  {renderFlag()}
                  <span>{profile.nationality}</span>
                </span>
              </div>

              <p className="text-slate-300 text-xs font-medium line-clamp-1">
                {profile.roleBadge}
              </p>
            </div>
          </div>

          {/* Right: Circular Headshot with White Border Ring (Exact Reference Style) */}
          <div className="shrink-0 self-center sm:self-start">
            <div
              className={`relative w-24 h-24 sm:w-28 sm:h-28 rounded-full border-4 border-white shadow-[0_10px_30px_rgba(0,0,0,0.5)] overflow-hidden bg-navy-dark transition-all duration-500 ${
                isHovered
                  ? 'ring-4 ring-cyan shadow-[0_0_35px_rgba(0,153,255,0.6)] scale-105'
                  : ''
              }`}
            >
              <img
                src={profile.image}
                alt={profile.name}
                className={`w-full h-full filter brightness-105 contrast-105 transition-transform duration-700 ${getImageStyling()}`}
              />
            </div>
          </div>
        </div>

        {/* MIDDLE: Testimonial/Vision Quote Body with Hover Effects */}
        <div className="relative z-10 mb-6">
          <div
            className={`p-5 rounded-2xl transition-all duration-300 ${
              isHovered
                ? 'bg-white/[0.08] border border-cyan/40 shadow-inner'
                : 'bg-white/[0.04] border border-white/10'
            }`}
          >
            <p className="text-slate-100 text-sm sm:text-base leading-relaxed font-normal">
              "{profile.quote || profile.experience[0]}"
            </p>
          </div>
        </div>

        {/* Verified Qualifications Strip (PhD, Erasmus Mundus, London Met) */}
        <div className="relative z-10 space-y-2 mb-6">
          {profile.qualifications.map((qual, qIdx) => (
            <div
              key={qIdx}
              className="flex items-center gap-2 text-xs sm:text-sm text-slate-200"
            >
              <CheckCircle2 className="w-4 h-4 text-cyan shrink-0" />
              <span>
                <strong className="text-white font-bold">{qual.degree}:</strong> {qual.institution}
                {qual.country ? ` (${qual.country})` : ''}
              </span>
            </div>
          ))}
        </div>

        {/* Star Rating Strip (Exact Reference Detail) */}
        <div className="relative z-10 flex items-center gap-2 mb-6">
          <div className="flex items-center gap-1 text-amber-400">
            {[...Array(5)].map((_, i) => (
              <Star key={i} className="w-5 h-5 fill-amber-400 text-amber-400" />
            ))}
          </div>
          <span className="text-xs font-semibold text-slate-300 ml-1">
            Verified Global Leadership Authority
          </span>
        </div>

        {/* BOTTOM ROW: Website Link / Dossier Action + Huge Closing Quote (Exact Poster Detail) */}
        <div className="relative z-10 pt-4 border-t border-white/15 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          {/* Website Link or Credentials Trigger */}
          <div className="flex items-center gap-4 flex-wrap">
            {profile.ventures && profile.ventures.length > 0 ? (
              <a
                href={getWebsiteHref()}
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-cyan hover:text-white transition-colors group/link"
              >
                <span>{getWebsiteUrl()}</span>
                <ExternalLink className="w-3.5 h-3.5 group-hover/link:translate-x-0.5 transition-transform" />
              </a>
            ) : (
              <span className="text-xs sm:text-sm font-semibold text-cyan">
                {getWebsiteUrl()}
              </span>
            )}

            <button
              onClick={onOpenDossier}
              className="px-4 py-2 rounded-xl bg-white/10 hover:bg-cyan text-white text-xs font-display font-bold uppercase tracking-wider transition-all duration-300 shadow-md hover:shadow-cyan-glow flex items-center gap-1.5"
            >
              <span>View Full Credentials</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </button>
          </div>

          {/* Huge Solid Cyan Closing Quote Mark (Exact Poster Positioning) */}
          <div
            className={`self-end sm:self-auto text-cyan/70 select-none pointer-events-none transition-transform duration-500 ${
              isHovered ? 'scale-110 text-cyan' : ''
            }`}
          >
            <div className="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-cyan/20 border border-cyan/40 flex items-center justify-center text-cyan shadow-[0_0_20px_rgba(0,153,255,0.3)]">
              <Quote className="w-8 h-8 sm:w-9 sm:h-9 text-cyan fill-cyan rotate-180" />
            </div>
          </div>
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
        return 'object-cover object-[50%_25%] scale-[1.2]';
      case 'muzaffar-hussain':
        return 'object-cover object-[50%_28%] scale-[1.2]';
      case 'suhaib-cheema':
        return 'object-cover object-[52%_15%] scale-[2.1] origin-[52%_15%]';
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
          {/* Left Column: Circular Headshot & Action */}
          <div className="lg:col-span-5 flex flex-col items-center text-center">
            <div className="relative w-36 h-36 sm:w-44 sm:h-44 rounded-full border-4 border-white shadow-[0_0_35px_rgba(0,153,255,0.4)] overflow-hidden bg-navy-dark mb-4">
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
