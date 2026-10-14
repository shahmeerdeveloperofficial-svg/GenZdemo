import React, { useState } from 'react';
import { motion } from 'framer-motion';
import {
  Quote,
  Sparkles,
  Users,
  Compass,
  Trophy,
  GraduationCap,
  ArrowRight,
  ShieldCheck,
  CheckCircle,
  Lightbulb,
  HeartHandshake
} from 'lucide-react';

interface CEOMessageSectionProps {
  onOpenConsultation?: () => void;
}

export const CEOMessageSection: React.FC<CEOMessageSectionProps> = ({ onOpenConsultation }) => {
  const [hoveredCard, setHoveredCard] = useState<number | null>(null);

  const impactMetrics = [
    { value: '50,000+', label: 'Individuals Trained', sub: 'Across Pakistan & Global Pathways', icon: Users },
    { value: '15+', label: 'Years Experience', sub: 'Personal & Human Development', icon: Trophy },
    { value: '1,500+', label: 'Life Mastery Scholars', sub: '3-Day Immersive Retreats', icon: Compass },
    { value: '1,000+', label: 'Youth Leaders Camp', sub: 'Empowered for Global Impact', icon: Sparkles }
  ];

  const focusPillars = [
    {
      title: 'Mindset & Self-Leadership',
      desc: 'Developing self-discipline, emotional resilience, and deep purpose before academic and career pursuits.',
      icon: Lightbulb
    },
    {
      title: 'Bridging Education & Enterprise',
      desc: 'Translating Cambridge credentials and degrees into high-income global careers and leadership capital.',
      icon: HeartHandshake
    },
    {
      title: 'Global Career Navigation',
      desc: 'Arming Pakistani youth with the strategic edge needed to outcompete internationally in the UK, Europe, and beyond.',
      icon: Compass
    }
  ];

  return (
    <section
      className="py-24 md:py-36 bg-gradient-to-b from-[#040E1B] via-[#07172C] to-[#0B2545] relative overflow-hidden text-white border-y border-cyan/20"
      id="ceo-message"
    >
      {/* Ambient Cyber Lighting / Logo Theme Glows */}
      <div className="absolute inset-0 pointer-events-none overflow-hidden">
        {/* Deep Cyan Center Spotlight */}
        <div className="absolute top-1/3 left-1/4 w-[36rem] h-[36rem] bg-cyan/15 rounded-full blur-[160px] animate-pulse" />
        {/* Electric Blue Orb near Portrait */}
        <div className="absolute bottom-1/4 right-10 w-[32rem] h-[32rem] bg-blue-600/20 rounded-full blur-[150px]" />
        {/* Subtle Tech Hex/Grid */}
        <div
          className="absolute inset-0 opacity-[0.035]"
          style={{
            backgroundImage: `radial-gradient(circle at 1.5px 1.5px, #0099FF 1px, transparent 0)`,
            backgroundSize: '48px 48px'
          }}
        />
      </div>

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Top Header Badge */}
        <div className="flex items-center justify-between flex-wrap gap-4 mb-12 pb-6 border-b border-white/10">
          <div className="inline-flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan via-blue-500 to-cyan p-[2px] shadow-[0_0_20px_rgba(0,153,255,0.4)]">
              <div className="w-full h-full rounded-[10px] bg-navy-dark flex items-center justify-center">
                <Quote className="w-5 h-5 text-cyan" />
              </div>
            </div>
            <div>
              <span className="text-xs font-display font-bold uppercase tracking-widest text-cyan-light">
                Executive Leadership Spotlight
              </span>
              <h4 className="text-white text-sm font-semibold">Message from the Chief Executive Officer</h4>
            </div>
          </div>

          <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs text-slate-300 backdrop-blur-md">
            <span className="w-2 h-2 rounded-full bg-emerald-400 animate-ping" />
            <span>Gen Z Consultant (Private Limited)</span>
          </div>
        </div>

        {/* Main Split Grid (Warren Buffet Inspired Cinematic Poster Style) */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
          {/* LEFT COLUMN: The Bold Quote, Message, and Philosophy */}
          <div className="lg:col-span-7 flex flex-col justify-center">
            {/* Fine Geometric Line / Bracket Accompanying Headline (Signature Poster Effect) */}
            <div className="relative pl-6 sm:pl-8 border-l-2 border-cyan/60 mb-8">
              {/* Top Accent Hook */}
              <div className="absolute top-0 left-[-2px] w-6 h-[2px] bg-cyan shadow-[0_0_10px_#0099FF]" />
              {/* Bottom Accent Hook */}
              <div className="absolute bottom-0 left-[-2px] w-10 h-[2px] bg-cyan shadow-[0_0_10px_#0099FF]" />

              <span className="text-xs font-display font-extrabold uppercase tracking-widest text-cyan mb-2 block">
                The Core Leadership Philosophy
              </span>

              <h2 className="font-display text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-[1.12]">
                "Converting <span className="text-transparent bg-clip-text bg-gradient-to-r from-cyan-light via-white to-cyan">Human Potential</span> into <span className="text-transparent bg-clip-text bg-gradient-to-r from-cyan via-blue-400 to-cyan-light">Human Capital</span>."
              </h2>
            </div>

            {/* CEO Narrative Text with Dynamic Hover Illumination */}
            <div className="space-y-4 mb-8">
              <motion.div
                whileHover={{ x: 4 }}
                className="p-5 rounded-2xl bg-white/[0.04] hover:bg-white/[0.07] border border-white/10 hover:border-cyan/40 transition-all duration-300 backdrop-blur-md group"
              >
                <p className="text-slate-200 text-base sm:text-lg leading-relaxed font-normal">
                  <strong className="text-white font-semibold group-hover:text-cyan transition-colors">
                    Muhammad Nawaz
                  </strong>{' '}
                  is a leadership and human development professional with over{' '}
                  <span className="text-cyan-light font-bold">15 years of proven experience</span> in personal transformation, executive coaching, and youth empowerment in Pakistan.
                </p>
              </motion.div>

              <motion.div
                whileHover={{ x: 4 }}
                className="p-5 rounded-2xl bg-white/[0.04] hover:bg-white/[0.07] border border-white/10 hover:border-cyan/40 transition-all duration-300 backdrop-blur-md group"
              >
                <p className="text-slate-300 text-sm sm:text-base leading-relaxed">
                  "At Gen Z Consultant, we are committed to bridging the gap between education, personal development, and career success. My mission is to develop capable, confident, and purpose-driven individuals who can lead themselves, advance their careers, and create a lasting positive impact on society."
                </p>
              </motion.div>
            </div>

            {/* 3 Strategic Focus Pillars with Hover Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-8">
              {focusPillars.map((pillar, idx) => {
                const Icon = pillar.icon;
                const isHovered = hoveredCard === idx;
                return (
                  <div
                    key={idx}
                    onMouseEnter={() => setHoveredCard(idx)}
                    onMouseLeave={() => setHoveredCard(null)}
                    className={`p-4 rounded-xl border transition-all duration-300 cursor-default ${
                      isHovered
                        ? 'bg-cyan/15 border-cyan shadow-[0_0_25px_rgba(0,153,255,0.35)] -translate-y-1'
                        : 'bg-white/[0.03] border-white/10 hover:border-white/25'
                    }`}
                  >
                    <Icon
                      className={`w-5 h-5 mb-2 transition-colors ${
                        isHovered ? 'text-cyan-light' : 'text-cyan'
                      }`}
                    />
                    <h5 className="font-display text-xs font-bold text-white mb-1 leading-snug">
                      {pillar.title}
                    </h5>
                    <p className="text-[11px] text-slate-400 leading-normal line-clamp-3">
                      {pillar.desc}
                    </p>
                  </div>
                );
              })}
            </div>

            {/* Verified Academic Credentials Strip */}
            <div className="p-4 rounded-2xl bg-navy-dark/70 border border-white/10 backdrop-blur-md flex flex-wrap items-center gap-3">
              <span className="text-xs font-display font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                <GraduationCap className="w-4 h-4 text-cyan" />
                <span>Degrees:</span>
              </span>
              <span className="px-3 py-1 rounded-lg bg-cyan/10 border border-cyan/25 text-cyan-light text-xs font-medium">
                MSc Waste Management & Environmental Sciences (United Kingdom)
              </span>
              <span className="px-3 py-1 rounded-lg bg-white/10 border border-white/20 text-slate-200 text-xs font-medium">
                MSc Geography (University of the Punjab, Pakistan)
              </span>
            </div>
          </div>

          {/* RIGHT COLUMN: Cinematic Portrait (Warren Buffet Style Showcase) */}
          <div className="lg:col-span-5 flex flex-col items-center">
            <div className="relative w-full max-w-[420px] group">
              {/* Glowing Outer Multi-Layer Aura */}
              <div className="absolute -inset-1.5 bg-gradient-to-tr from-cyan via-blue-500 to-cyan rounded-3xl opacity-75 group-hover:opacity-100 blur-xl transition-all duration-500" />

              {/* Main Card Frame */}
              <div className="relative rounded-3xl overflow-hidden bg-navy-dark border-2 border-cyan/40 shadow-[0_20px_60px_rgba(0,0,0,0.8)]">
                {/* Portrait Image */}
                <div className="relative aspect-[4/5] overflow-hidden">
                  <img
                    src="/assets/images/team/muhammad-nawaz.jpg"
                    alt="Muhammad Nawaz - CEO Gen Z Consultant"
                    className="w-full h-full object-cover object-top filter brightness-105 contrast-105 group-hover:scale-105 transition-transform duration-700 ease-out"
                  />

                  {/* Gradient Fade Overlays for Cinematic Depth */}
                  <div className="absolute inset-0 bg-gradient-to-t from-navy-dark via-navy-dark/30 to-transparent opacity-90" />
                  <div className="absolute inset-0 bg-gradient-to-r from-navy-dark/40 via-transparent to-navy-dark/30 pointer-events-none" />

                  {/* Top Floating Badge */}
                  <div className="absolute top-4 left-4 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-navy-deep/85 backdrop-blur-md border border-cyan/30 text-white text-xs font-semibold shadow-lg">
                    <ShieldCheck className="w-4 h-4 text-cyan" />
                    <span>Chief Executive Officer</span>
                  </div>

                  {/* Top Right Flag */}
                  <div className="absolute top-4 right-4 px-2.5 py-1 rounded-full bg-navy-deep/85 backdrop-blur-md border border-white/20 text-sm shadow-lg">
                    🇵🇰 Pakistan
                  </div>

                  {/* Bottom Typography (Warren Buffet Signature Placement) */}
                  <div className="absolute bottom-6 left-6 right-6 z-10">
                    <div className="text-cyan text-xs font-display font-extrabold uppercase tracking-widest mb-1 flex items-center gap-1.5">
                      <Sparkles className="w-3.5 h-3.5 text-cyan" />
                      <span>Executive Leadership</span>
                    </div>
                    <h3 className="font-display text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight">
                      Muhammad Nawaz
                    </h3>
                    <p className="text-slate-300 text-xs sm:text-sm font-medium mt-1 leading-snug">
                      Chief Executive Officer (CEO)
                      <br />
                      <span className="text-cyan-light">Human Development & Leadership Consultant</span>
                    </p>
                  </div>
                </div>

                {/* Bottom Impact Stats Strip */}
                <div className="p-4 bg-gradient-to-r from-[#040E1B] to-[#07172C] border-t border-cyan/20">
                  <div className="grid grid-cols-2 gap-3 text-center">
                    <div className="p-2.5 rounded-xl bg-white/[0.03] border border-white/10">
                      <div className="font-display text-xl font-black text-cyan">50,000+</div>
                      <div className="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">
                        Individuals Trained
                      </div>
                    </div>
                    <div className="p-2.5 rounded-xl bg-white/[0.03] border border-white/10">
                      <div className="font-display text-xl font-black text-white">15+ Years</div>
                      <div className="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">
                        Executive Track Record
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {/* Heavy Advisory CTA Button below Portrait */}
            <button
              onClick={onOpenConsultation}
              className="mt-6 w-full max-w-[420px] group relative px-6 py-4 rounded-2xl bg-gradient-to-r from-cyan via-blue-500 to-cyan bg-[length:200%_auto] hover:bg-right font-display text-sm font-bold text-white shadow-[0_0_35px_rgba(0,153,255,0.4)] hover:shadow-[0_0_50px_rgba(0,153,255,0.7)] transition-all duration-500 hover:scale-105 active:scale-95 flex items-center justify-center gap-2"
            >
              <Sparkles className="w-4 h-4 text-white" />
              <span>Connect with CEO & Mentorship Office</span>
              <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
            </button>
          </div>
        </div>

        {/* 4 Bottom Metric Cards Across Section */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mt-16 pt-12 border-t border-white/10">
          {impactMetrics.map((metric, mIdx) => {
            const Icon = metric.icon;
            return (
              <motion.div
                key={mIdx}
                whileHover={{ y: -4, borderColor: '#0099FF' }}
                className="p-5 rounded-2xl bg-white/[0.03] hover:bg-white/[0.06] border border-white/10 transition-all duration-300"
              >
                <Icon className="w-5 h-5 text-cyan mb-2" />
                <div className="font-display text-2xl sm:text-3xl font-black text-white mb-1">
                  {metric.value}
                </div>
                <div className="text-xs font-semibold text-slate-200">{metric.label}</div>
                <div className="text-[11px] text-slate-400 mt-0.5">{metric.sub}</div>
              </motion.div>
            );
          })}
        </div>
      </div>
    </section>
  );
};
