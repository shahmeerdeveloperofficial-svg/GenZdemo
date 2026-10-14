import React, { useState, useRef } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
  Award,
  Globe,
  GraduationCap,
  Briefcase,
  ExternalLink,
  ShieldCheck,
  Sparkles,
  ChevronRight,
  X,
  Building2,
  FileCheck2,
  Star,
  CheckCircle2,
  MapPin,
  ArrowUpRight
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

  const filteredProfiles = LEADERSHIP_PROFILES.filter((p) => {
    if (selectedFilter === 'ALL') return true;
    return p.countryCode === selectedFilter;
  });

  return (
    <section
      className="py-24 md:py-32 bg-gradient-to-b from-navy-deep via-navy-dark to-navy relative overflow-hidden text-white"
      id="leadership"
    >
      {/* Ambient 3D Cyber Background Lights */}
      <div className="absolute inset-0 pointer-events-none overflow-hidden">
        {/* Glowing Orb 1: Cyan */}
        <div className="absolute top-1/4 -left-48 w-96 h-96 bg-cyan/20 rounded-full blur-[140px] animate-pulse" />
        {/* Glowing Orb 2: Purple / Blue */}
        <div className="absolute bottom-1/4 -right-48 w-[30rem] h-[30rem] bg-blue-600/20 rounded-full blur-[160px]" />
        {/* Glowing Orb 3: Gold Accent */}
        <div className="absolute top-2/3 left-1/3 w-80 h-80 bg-amber-500/10 rounded-full blur-[130px]" />
        {/* Tech Grid Lines */}
        <div
          className="absolute inset-0 opacity-[0.03]"
          style={{
            backgroundImage: `radial-gradient(circle at 1px 1px, #ffffff 1px, transparent 0)`,
            backgroundSize: '40px 40px'
          }}
        />
      </div>

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Header & Section Title */}
        <div className="text-center max-w-3xl mx-auto mb-16">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/10 border border-cyan/30 text-cyan text-xs font-display font-bold uppercase tracking-widest shadow-[0_0_20px_rgba(0,153,255,0.3)] mb-5"
          >
            <Sparkles className="w-3.5 h-3.5 text-cyan animate-spin-slow" />
            <span>Executive Directorate & Global Advisory</span>
          </motion.div>

          <motion.h2
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.1 }}
            className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-[1.18] mb-6"
          >
            World-Class Minds Behind Your Child’s International Horizon<span className="heading-cursor-line">|</span>
          </motion.h2>

          <motion.p
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.2 }}
            className="text-slate-300 text-base sm:text-lg leading-relaxed"
          >
            Led by distinguished European doctoral scholars, British enterprise directors, and international mentors from Aarhus, Copenhagen, Bangor, and London — forging direct conduits to the world’s most prestigious universities and global ventures.
          </motion.p>

          {/* Interactive Filter Pills */}
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.3 }}
            className="flex flex-wrap items-center justify-center gap-2 sm:gap-3 mt-8"
          >
            {[
              { id: 'ALL', label: 'All Executive Directorate', count: 4, icon: Globe },
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
                  className={`relative px-4 py-2.5 rounded-xl text-xs sm:text-sm font-display font-semibold transition-all duration-300 flex items-center gap-2 ${
                    active
                      ? 'bg-gradient-to-r from-cyan to-blue-600 text-white shadow-[0_0_25px_rgba(0,153,255,0.5)] scale-105'
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
                    className={`ml-1 text-[11px] px-1.5 py-0.5 rounded-full ${
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

        {/* 3D Grid of Executive Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-7">
          <AnimatePresence mode="popLayout">
            {filteredProfiles.map((profile, index) => (
              <LeadershipCard3D
                key={profile.id}
                profile={profile}
                index={index}
                onOpenDossier={() => setActiveDossier(profile)}
              />
            ))}
          </AnimatePresence>
        </div>

        {/* Heavy Bottom Banner / Advisory CTA */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="mt-16 p-8 sm:p-10 rounded-3xl bg-gradient-to-r from-white/[0.07] via-cyan/[0.08] to-white/[0.05] border border-cyan/30 backdrop-blur-xl relative overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)]"
        >
          <div className="absolute -right-20 -bottom-20 w-80 h-80 bg-cyan/20 rounded-full blur-[90px] pointer-events-none" />
          <div className="flex flex-col lg:flex-row items-center justify-between gap-8 relative z-10">
            <div className="max-w-2xl text-center lg:text-left">
              <div className="inline-flex items-center gap-2 text-cyan text-xs font-display font-bold uppercase tracking-wider mb-2">
                <ShieldCheck className="w-4 h-4 text-cyan" />
                <span>Direct Access to Global Directorate</span>
              </div>
              <h3 className="font-display text-2xl sm:text-3xl font-extrabold text-white tracking-tight mb-2">
                Seeking Erasmus Mundus, European Admissions, or UK Visas?
              </h3>
              <p className="text-slate-300 text-sm sm:text-base leading-relaxed">
                Our directors personally review qualifying GenZ scholars and study abroad candidates for fully-funded international scholarships and British entrepreneurial programs.
              </p>
            </div>

            <div className="flex flex-wrap items-center justify-center gap-4">
              <button
                onClick={() => onOpenConsultation?.()}
                className="group relative px-7 py-4 rounded-2xl bg-gradient-to-r from-cyan via-blue-500 to-cyan bg-[length:200%_auto] hover:bg-right font-display text-sm font-bold text-white shadow-[0_0_35px_rgba(0,153,255,0.45)] hover:shadow-[0_0_50px_rgba(0,153,255,0.7)] transition-all duration-500 hover:scale-105 active:scale-95 flex items-center gap-2"
              >
                <Sparkles className="w-4 h-4 text-white" />
                <span>Request Executive Directorate Advisory</span>
                <ChevronRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
              </button>
            </div>
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
/* 3D TILT INTERACTIVE CARD                                                  */
/* -------------------------------------------------------------------------- */
interface LeadershipCardProps {
  profile: LeadershipProfile;
  index: number;
  onOpenDossier: () => void;
}

const LeadershipCard3D: React.FC<LeadershipCardProps> = ({ profile, index, onOpenDossier }) => {
  const cardRef = useRef<HTMLDivElement>(null);
  const [rotateX, setRotateX] = useState(0);
  const [rotateY, setRotateY] = useState(0);
  const [glarePos, setGlarePos] = useState({ x: 50, y: 50, opacity: 0 });

  const handleMouseMove = (e: React.MouseEvent<HTMLDivElement>) => {
    if (!cardRef.current) return;
    const rect = cardRef.current.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    const centerX = rect.width / 2;
    const centerY = rect.height / 2;

    // Maximum tilt angles
    const rotX = -((y - centerY) / centerY) * 12;
    const rotY = ((x - centerX) / centerX) * 12;

    setRotateX(rotX);
    setRotateY(rotY);
    setGlarePos({
      x: (x / rect.width) * 100,
      y: (y / rect.height) * 100,
      opacity: 0.25
    });
  };

  const handleMouseLeave = () => {
    setRotateX(0);
    setRotateY(0);
    setGlarePos((prev) => ({ ...prev, opacity: 0 }));
  };

  const renderFlag = () => {
    if (profile.countryCode === 'DK') return <DenmarkFlag className="w-5 h-3.5 rounded-sm shadow-sm" />;
    if (profile.countryCode === 'GB') return <UKFlag className="w-5 h-3.5 rounded-sm shadow-sm" />;
    if (profile.countryCode === 'PK') return <PakistanFlag className="w-5 h-3.5 rounded-sm shadow-sm" />;
    return <span className="text-sm">{profile.countryFlagEmoji}</span>;
  };

  return (
    <motion.div
      layout
      initial={{ opacity: 0, y: 30 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, scale: 0.95 }}
      transition={{ duration: 0.5, delay: index * 0.1 }}
      className="perspective-1000 h-full"
    >
      <div
        ref={cardRef}
        onMouseMove={handleMouseMove}
        onMouseLeave={handleMouseLeave}
        style={{
          transform: `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`,
          transformStyle: 'preserve-3d',
          transition: 'transform 0.15s ease-out'
        }}
        className="group relative h-full rounded-3xl bg-gradient-to-b from-white/[0.08] via-navy-light/40 to-navy-dark/90 border border-white/10 hover:border-cyan/50 p-6 flex flex-col justify-between shadow-[0_15px_35px_rgba(0,0,0,0.4)] hover:shadow-[0_20px_60px_rgba(0,153,255,0.25)] transition-all duration-300"
      >
        {/* Dynamic Holographic Mouse Glare */}
        <div
          className="absolute inset-0 rounded-3xl pointer-events-none transition-opacity duration-300"
          style={{
            background: `radial-gradient(circle at ${glarePos.x}% ${glarePos.y}%, rgba(0, 153, 255, ${glarePos.opacity}) 0%, transparent 60%)`
          }}
        />

        {/* Glowing Top Corner Accent */}
        <div className="absolute top-0 right-0 w-28 h-28 bg-gradient-to-bl from-cyan/20 to-transparent rounded-tr-3xl pointer-events-none" />

        {/* 3D Floating Content Layer */}
        <div style={{ transform: 'translateZ(40px)', transformStyle: 'preserve-3d' }}>
          {/* Portrait & Country Badge */}
          <div className="relative mb-5 flex justify-center">
            <div className="relative w-full aspect-[4/4.6] rounded-2xl overflow-hidden border-2 border-white/15 group-hover:border-cyan/60 shadow-[0_10px_25px_rgba(0,0,0,0.5)] bg-navy-dark transition-all duration-500">
              {profile.image ? (
                <img
                  src={profile.image}
                  alt={profile.name}
                  className="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-700 ease-out"
                  onError={(e) => {
                    // Fallback to stylized monogram avatar
                    (e.target as HTMLElement).style.display = 'none';
                    const fallback = (e.target as HTMLElement).nextElementSibling;
                    if (fallback) fallback.classList.remove('hidden');
                  }}
                />
              ) : null}

              {/* Stylized Executive Monogram Fallback */}
              <div
                className={`${
                  profile.image ? 'hidden' : 'flex'
                } w-full h-full flex-col items-center justify-center bg-gradient-to-br from-navy via-navy-light to-navy-deep p-6 text-center`}
              >
                <div className="w-20 h-20 rounded-2xl bg-gradient-to-tr from-cyan via-blue-600 to-amber-400 p-[2px] mb-3 shadow-[0_0_20px_rgba(0,153,255,0.4)]">
                  <div className="w-full h-full rounded-2xl bg-navy-dark flex items-center justify-center">
                    <span className="font-display font-extrabold text-2xl text-cyan">
                      {profile.name
                        .split(' ')
                        .map((n) => n[0])
                        .slice(0, 2)
                        .join('')}
                    </span>
                  </div>
                </div>
                <div className="text-xs font-display font-bold text-amber-400 uppercase tracking-widest">
                  Executive Office
                </div>
                <div className="text-[11px] text-slate-400 mt-1">Global Governance</div>
              </div>

              {/* Bottom Subtle Gradient on Image */}
              <div className="absolute inset-0 bg-gradient-to-t from-navy-dark via-transparent to-transparent opacity-80" />

              {/* Floating Country Badge on Top Left */}
              <div
                style={{ transform: 'translateZ(25px)' }}
                className="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-navy-deep/80 backdrop-blur-md border border-white/20 text-white text-[11px] font-display font-semibold shadow-md"
              >
                {renderFlag()}
                <span>{profile.nationality}</span>
              </div>

              {/* Title Badge on Top Right */}
              <div
                style={{ transform: 'translateZ(25px)' }}
                className="absolute top-3 right-3 px-2.5 py-1 rounded-full bg-cyan/20 backdrop-blur-md border border-cyan/40 text-cyan-light text-[11px] font-display font-bold uppercase tracking-wider"
              >
                {profile.title}
              </div>

              {/* Bottom Tagline on Image */}
              <div className="absolute bottom-3 left-3 right-3 z-10">
                <div className="text-[11px] font-medium text-cyan-light line-clamp-1 flex items-center gap-1">
                  <GraduationCap className="w-3.5 h-3.5 shrink-0" />
                  <span>{profile.featuredBadge}</span>
                </div>
              </div>
            </div>
          </div>

          {/* Profile Details */}
          <div className="mb-4">
            <h3 className="font-display text-xl font-bold text-white group-hover:text-cyan transition-colors leading-tight mb-1">
              {profile.name}
            </h3>
            <p className="text-xs font-medium text-slate-400 line-clamp-1 mb-3">
              {profile.roleBadge}
            </p>

            {/* Academic Credentials Mini-Ribbon */}
            <div className="space-y-1.5 pt-2 border-t border-white/10">
              {profile.qualifications.slice(0, 2).map((q, qIdx) => (
                <div key={qIdx} className="flex items-start gap-1.5 text-xs text-slate-300">
                  <CheckCircle2 className="w-3.5 h-3.5 text-cyan shrink-0 mt-0.5" />
                  <span className="line-clamp-1">
                    <strong className="text-white font-semibold">{q.degree}:</strong> {q.institution}
                  </span>
                </div>
              ))}
            </div>

            {/* Ventures / Highlights snippet */}
            {profile.ventures && profile.ventures.length > 0 && (
              <div className="mt-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-300 text-[11px] font-medium">
                <Building2 className="w-3 h-3 text-amber-400" />
                <span className="line-clamp-1">{profile.ventures[0].name}</span>
              </div>
            )}
          </div>
        </div>

        {/* Heavy Action Button */}
        <div style={{ transform: 'translateZ(30px)' }} className="mt-auto pt-3">
          <button
            onClick={onOpenDossier}
            className="w-full relative group/btn overflow-hidden rounded-xl bg-white/10 hover:bg-cyan p-[1px] transition-all duration-300"
          >
            <div className="w-full py-2.5 px-4 rounded-[11px] bg-navy-dark/90 group-hover/btn:bg-transparent transition-colors flex items-center justify-center gap-2">
              <span className="font-display text-xs font-bold text-white tracking-wide uppercase">
                View Full Credentials
              </span>
              <ArrowUpRight className="w-3.5 h-3.5 text-cyan group-hover/btn:text-white transition-colors" />
            </div>
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
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
      {/* Backdrop */}
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
        onClick={onClose}
        className="fixed inset-0 bg-navy-deep/80 backdrop-blur-xl"
      />

      {/* Modal Dialog Card */}
      <motion.div
        initial={{ opacity: 0, scale: 0.9, y: 30 }}
        animate={{ opacity: 1, scale: 1, y: 0 }}
        exit={{ opacity: 0, scale: 0.9, y: 30 }}
        transition={{ type: 'spring', damping: 25, stiffness: 300 }}
        className="relative w-full max-w-4xl bg-gradient-to-b from-navy-light/95 via-navy-dark/95 to-navy-deep/98 border border-cyan/40 rounded-3xl p-6 sm:p-10 shadow-[0_25px_80px_rgba(0,0,0,0.8)] text-white z-10 max-h-[90vh] overflow-y-auto"
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
          {/* Left Column: Portrait & Key Stats */}
          <div className="lg:col-span-5 flex flex-col items-center text-center">
            <div className="relative w-full max-w-[260px] aspect-[4/5] rounded-2xl overflow-hidden border-2 border-cyan/50 shadow-[0_0_30px_rgba(0,153,255,0.3)] bg-navy-dark mb-4">
              {profile.image ? (
                <img
                  src={profile.image}
                  alt={profile.name}
                  className="w-full h-full object-cover object-top"
                  onError={(e) => {
                    (e.target as HTMLElement).style.display = 'none';
                    const fallback = (e.target as HTMLElement).nextElementSibling;
                    if (fallback) fallback.classList.remove('hidden');
                  }}
                />
              ) : null}

              {/* Stylized Executive Monogram Fallback */}
              <div
                className={`${
                  profile.image ? 'hidden' : 'flex'
                } w-full h-full flex-col items-center justify-center bg-gradient-to-br from-navy via-navy-light to-navy-deep p-6 text-center`}
              >
                <div className="w-24 h-24 rounded-2xl bg-gradient-to-tr from-cyan via-blue-600 to-amber-400 p-[2px] mb-3 shadow-[0_0_30px_rgba(0,153,255,0.4)]">
                  <div className="w-full h-full rounded-2xl bg-navy-dark flex items-center justify-center">
                    <span className="font-display font-extrabold text-3xl text-cyan">
                      {profile.name
                        .split(' ')
                        .map((n) => n[0])
                        .slice(0, 2)
                        .join('')}
                    </span>
                  </div>
                </div>
                <div className="text-xs font-display font-bold text-amber-400 uppercase tracking-widest">
                  Executive Office
                </div>
              </div>

              {/* Verified Ribbon */}
              <div className="absolute bottom-3 left-3 right-3 py-1.5 px-3 rounded-lg bg-navy-deep/85 backdrop-blur-md border border-white/20 text-xs font-semibold flex items-center justify-center gap-1.5 text-cyan-light">
                <ShieldCheck className="w-4 h-4 text-cyan" />
                <span>Verified Global Directorate</span>
              </div>
            </div>

            {/* Nationality & Role Meta */}
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-display font-semibold mb-2">
              <MapPin className="w-3.5 h-3.5 text-cyan" />
              <span>
                {profile.nationality} {profile.countryFlagEmoji}
              </span>
            </div>

            <p className="text-xs text-slate-400 font-medium">{profile.roleBadge}</p>

            {/* Quick Action Button in Left Column */}
            <button
              onClick={onConsult}
              className="mt-6 w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-cyan via-blue-500 to-cyan bg-[length:200%_auto] hover:bg-right font-display text-xs sm:text-sm font-bold text-white shadow-[0_0_25px_rgba(0,153,255,0.4)] hover:shadow-[0_0_40px_rgba(0,153,255,0.6)] transition-all flex items-center justify-center gap-2"
            >
              <Sparkles className="w-4 h-4" />
              <span>Book Directorate Consultation</span>
            </button>
          </div>

          {/* Right Column: Full Academic & Industry Credentials */}
          <div className="lg:col-span-7 space-y-6">
            <div>
              <div className="inline-block text-xs font-display font-bold text-cyan uppercase tracking-wider mb-1">
                {profile.title} Profile Dossier
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

            {/* Commercial Ventures & Industry Leadership (if present) */}
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

            {/* Core Specialization Badges */}
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
