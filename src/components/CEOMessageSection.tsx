import React, { useRef, useState } from 'react';
import { motion, useMotionValue, useSpring, useTransform } from 'framer-motion';
import {
  Sparkles,
  ArrowRight,
  GraduationCap,
  Trophy,
  Users,
  Compass,
  Award,
  CheckCircle2,
  ShieldCheck,
  Zap,
  TrendingUp,
  Target
} from 'lucide-react';

interface CEOMessageSectionProps {
  onOpenConsultation?: () => void;
}

export const CEOMessageSection: React.FC<CEOMessageSectionProps> = ({ onOpenConsultation }) => {
  // 3D Mouse Parallax Tilt for the CEO Showcase Card
  const cardRef = useRef<HTMLDivElement>(null);
  const [isHovered, setIsHovered] = useState(false);

  const mouseX = useMotionValue(0);
  const mouseY = useMotionValue(0);

  const springConfig = { damping: 20, stiffness: 200 };
  const rotateX = useSpring(useTransform(mouseY, [-0.5, 0.5], [8, -8]), springConfig);
  const rotateY = useSpring(useTransform(mouseX, [-0.5, 0.5], [-8, 8]), springConfig);

  const handleMouseMove = (e: React.MouseEvent<HTMLDivElement>) => {
    if (!cardRef.current) return;
    const rect = cardRef.current.getBoundingClientRect();
    const x = (e.clientX - rect.left) / rect.width - 0.5;
    const y = (e.clientY - rect.top) / rect.height - 0.5;
    mouseX.set(x);
    mouseY.set(y);
  };

  const handleMouseLeave = () => {
    mouseX.set(0);
    mouseY.set(0);
    setIsHovered(false);
  };

  const impactMetrics = [
    { value: '50,000+', label: 'Individuals Trained', sub: 'Across Personal & Executive Programs', icon: Users },
    { value: '15+', label: 'Years Experience', sub: 'Leadership & Human Development', icon: Trophy },
    { value: '1,500+', label: 'Life Mastery Scholars', sub: '3-Day Immersive Retreats', icon: Compass },
    { value: '1,000+', label: 'Youth Leaders Camp', sub: 'Summer Leadership Programs', icon: Target }
  ];

  return (
    <section
      className="py-24 md:py-32 bg-gradient-to-br from-[#030B17] via-[#07172C] to-[#0A2548] relative overflow-hidden text-white border-y border-cyan/25"
      id="ceo-message"
    >
      {/* 3D Ambient Animated Glowing Orbs & Tech Particles */}
      <div className="absolute inset-0 pointer-events-none overflow-hidden">
        {/* Animated Radial Beam 1: Electric Cyan */}
        <motion.div
          animate={{
            scale: [1, 1.15, 1],
            opacity: [0.2, 0.35, 0.2]
          }}
          transition={{ duration: 8, repeat: Infinity, ease: 'easeInOut' }}
          className="absolute top-1/4 -left-32 w-[38rem] h-[38rem] bg-cyan/20 rounded-full blur-[160px]"
        />

        {/* Animated Radial Beam 2: Royal Blue */}
        <motion.div
          animate={{
            scale: [1.1, 1, 1.1],
            opacity: [0.25, 0.4, 0.25]
          }}
          transition={{ duration: 10, repeat: Infinity, ease: 'easeInOut' }}
          className="absolute top-1/3 -right-32 w-[42rem] h-[42rem] bg-blue-600/25 rounded-full blur-[180px]"
        />

        {/* Central Luminous Core */}
        <div className="absolute bottom-10 left-1/3 w-[35rem] h-[35rem] bg-cyan/10 rounded-full blur-[180px]" />

        {/* High-Tech Circuit Matrix Overlay */}
        <div
          className="absolute inset-0 opacity-[0.035]"
          style={{
            backgroundImage: `radial-gradient(circle at 1.5px 1.5px, #0099FF 1px, transparent 0)`,
            backgroundSize: '36px 36px'
          }}
        />

        {/* Animated Cyber Slashes */}
        <div className="absolute top-20 right-1/4 w-96 h-[1px] bg-gradient-to-r from-transparent via-cyan/40 to-transparent rotate-12" />
        <div className="absolute bottom-32 left-1/5 w-80 h-[1px] bg-gradient-to-r from-transparent via-blue-500/30 to-transparent -rotate-12" />
      </div>

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Top Tag Badge */}
        <div className="flex items-center justify-between flex-wrap gap-4 mb-10 pb-5 border-b border-white/10">
          <div className="inline-flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan via-blue-500 to-cyan p-[2px] shadow-[0_0_20px_rgba(0,153,255,0.4)] animate-pulse">
              <div className="w-full h-full rounded-[10px] bg-navy-dark flex items-center justify-center">
                <Zap className="w-5 h-5 text-cyan" />
              </div>
            </div>
            <div>
              <span className="text-xs font-display font-extrabold uppercase tracking-widest text-cyan-light flex items-center gap-1.5">
                <span>Executive Leadership Spotlight</span>
                <span className="w-1.5 h-1.5 rounded-full bg-cyan animate-ping" />
              </span>
              <h4 className="text-white text-sm font-semibold">
                Keynote Message from Chief Executive Officer
              </h4>
            </div>
          </div>

          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/10 border border-cyan/30 text-xs font-display font-bold text-cyan-light backdrop-blur-md shadow-[0_0_15px_rgba(0,153,255,0.2)]">
            <ShieldCheck className="w-4 h-4 text-cyan" />
            <span>Gen Z Consultant (Private Limited)</span>
          </div>
        </div>

        {/* MAIN SPLIT POSTER HERO: Narrative on Left + 3D Tilt Heroic Portrait on Right */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
          {/* LEFT COLUMN: The Bold Quote, Message, and Philosophy */}
          <div className="lg:col-span-6 flex flex-col justify-center">
            {/* Signature L-Bracket Line & Keynote Headline */}
            <div className="relative pl-6 sm:pl-8 border-l-4 border-cyan mb-8">
              {/* Top Accent Dot */}
              <div className="absolute -top-1.5 -left-[7px] w-3 h-3 rounded-full bg-cyan shadow-[0_0_10px_#0099FF]" />
              {/* Bottom Accent Dot */}
              <div className="absolute -bottom-1.5 -left-[7px] w-3 h-3 rounded-full bg-cyan shadow-[0_0_10px_#0099FF]" />

              <span className="text-xs font-display font-extrabold uppercase tracking-widest text-cyan mb-2 block">
                The Core Leadership Philosophy
              </span>

              <h2 className="font-display text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-[1.12]">
                "Converting{' '}
                <span className="text-transparent bg-clip-text bg-gradient-to-r from-cyan-light via-cyan to-blue-400">
                  Human Potential
                </span>{' '}
                into{' '}
                <span className="text-transparent bg-clip-text bg-gradient-to-r from-cyan via-blue-400 to-cyan-light">
                  Human Capital
                </span>
                ."
              </h2>
            </div>

            {/* CEO Narrative Text with Glowing Micro-Cards */}
            <div className="space-y-4 mb-8">
              <motion.div
                whileHover={{ x: 5, backgroundColor: 'rgba(255, 255, 255, 0.08)' }}
                className="p-5 rounded-2xl bg-white/[0.04] border border-white/10 hover:border-cyan/50 transition-all duration-300 backdrop-blur-md shadow-lg group"
              >
                <p className="text-slate-200 text-base sm:text-lg leading-relaxed font-normal">
                  <strong className="text-white font-semibold group-hover:text-cyan transition-colors">
                    Muhammad Nawaz
                  </strong>{' '}
                  is a leadership and human development professional with over{' '}
                  <span className="text-cyan-light font-bold">15 years of proven authority</span> in personal transformation, executive coaching, and youth empowerment in Pakistan.
                </p>
              </motion.div>

              <motion.div
                whileHover={{ x: 5, backgroundColor: 'rgba(255, 255, 255, 0.08)' }}
                className="p-5 rounded-2xl bg-white/[0.04] border border-white/10 hover:border-cyan/50 transition-all duration-300 backdrop-blur-md shadow-lg group"
              >
                <p className="text-slate-300 text-sm sm:text-base leading-relaxed">
                  "At Gen Z Consultant, we bridge the gap between academic education, personal growth, and global career success. My mission is to develop capable, confident, and purpose-driven leaders who can lead themselves, advance their careers, and create a lasting positive impact on society."
                </p>
              </motion.div>
            </div>

            {/* Verified Academic Degrees Badges */}
            <div className="flex flex-wrap items-center gap-2.5 mb-8">
              <span className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-cyan/15 border border-cyan/40 text-cyan-light text-xs font-semibold shadow-sm hover:scale-105 transition-transform">
                <GraduationCap className="w-4 h-4 text-cyan" />
                <span>MSc Waste Management & Environmental Sciences — United Kingdom</span>
              </span>
              <span className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/10 border border-white/20 text-slate-200 text-xs font-semibold shadow-sm hover:scale-105 transition-transform">
                <GraduationCap className="w-4 h-4 text-slate-300" />
                <span>MSc Geography — University of the Punjab, Pakistan</span>
              </span>
            </div>

            {/* Heavy Animated CTA Button */}
            <div>
              <button
                onClick={onOpenConsultation}
                className="group relative px-8 py-4 rounded-2xl bg-gradient-to-r from-cyan via-blue-500 to-cyan bg-[length:200%_auto] hover:bg-right font-display text-sm font-bold text-white shadow-[0_0_35px_rgba(0,153,255,0.45)] hover:shadow-[0_0_55px_rgba(0,153,255,0.75)] transition-all duration-500 hover:scale-105 active:scale-95 flex items-center gap-3"
              >
                <Sparkles className="w-4 h-4 text-white animate-spin-slow" />
                <span>Connect with CEO & Mentorship Office</span>
                <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
              </button>
            </div>
          </div>

          {/* RIGHT COLUMN: 3D Parallax Tilt Portrait (100% Unclipped Head, Eye-Touching Polish) */}
          <div className="lg:col-span-6 flex justify-center items-center perspective-1000">
            <motion.div
              ref={cardRef}
              style={{
                rotateX,
                rotateY,
                transformStyle: 'preserve-3d'
              }}
              onMouseMove={handleMouseMove}
              onMouseEnter={() => setIsHovered(true)}
              onMouseLeave={handleMouseLeave}
              className="relative w-full max-w-[480px] rounded-[2.5rem] bg-gradient-to-b from-[#0F356B] via-[#0B2545] to-[#040E1B] p-2.5 sm:p-3 border-2 border-cyan/40 shadow-[0_30px_90px_rgba(0,0,0,0.85)] group cursor-pointer transition-shadow duration-500 hover:border-cyan hover:shadow-[0_35px_100px_rgba(0,153,255,0.4)]"
            >
              {/* Outer Pulsing Neon Aura */}
              <div className="absolute -inset-2 bg-gradient-to-r from-cyan via-blue-500 to-cyan rounded-[3rem] opacity-40 group-hover:opacity-80 blur-xl transition-all duration-500 pointer-events-none" />

              {/* Main Photo Frame (With breathing room at top so his head is NEVER cut off!) */}
              <div className="relative rounded-[2rem] overflow-hidden bg-[#07172C] aspect-[4/4.8]">
                {/* Background Ambient Radial Light behind his head */}
                <div className="absolute top-6 left-1/4 w-64 h-64 bg-cyan/25 rounded-full blur-[80px] pointer-events-none" />

                {/* THE HEROIC PORTRAIT: Perfectly focused on face, 100% of head and hair visible! */}
                <img
                  src="/assets/images/team/muhammad-nawaz.jpg"
                  alt="Muhammad Nawaz - CEO Gen Z Consultant"
                  className="w-full h-full object-cover object-[30%_3%] transition-transform duration-700 ease-out group-hover:scale-105"
                />

                {/* Smooth Vignette Fades at Bottom and Edges */}
                <div className="absolute inset-x-0 bottom-0 h-44 bg-gradient-to-t from-[#040E1B] via-[#040E1B]/80 to-transparent pointer-events-none" />
                <div className="absolute inset-y-0 left-0 w-16 bg-gradient-to-r from-[#040E1B]/50 to-transparent pointer-events-none" />

                {/* Floating 3D Badge Top-Right: Nationality */}
                <div
                  style={{ transform: 'translateZ(45px)' }}
                  className="absolute top-4 right-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#040E1B]/85 backdrop-blur-md border border-cyan/40 text-white text-xs font-display font-bold shadow-lg"
                >
                  <span>🇵🇰</span>
                  <span>Pakistan HQ</span>
                </div>

                {/* Floating 3D Badge Top-Left: Authority Tag */}
                <div
                  style={{ transform: 'translateZ(45px)' }}
                  className="absolute top-4 left-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-cyan/20 backdrop-blur-md border border-cyan/50 text-cyan-light text-xs font-display font-bold shadow-lg"
                >
                  <ShieldCheck className="w-3.5 h-3.5 text-cyan" />
                  <span>Chief Executive Officer</span>
                </div>

                {/* Overlaid Bold Typography at Bottom (Warren Buffet Style Placement) */}
                <div
                  style={{ transform: 'translateZ(55px)' }}
                  className="absolute bottom-5 left-5 right-5 z-20"
                >
                  <div className="text-cyan text-xs font-display font-extrabold uppercase tracking-widest mb-1 flex items-center gap-1.5">
                    <Sparkles className="w-3.5 h-3.5 text-cyan" />
                    <span>Executive Leadership</span>
                  </div>

                  <h3 className="font-display text-3xl sm:text-4xl font-black text-white tracking-tight leading-none drop-shadow-[0_4px_12px_rgba(0,0,0,0.8)]">
                    Muhammad Nawaz
                  </h3>

                  <p className="text-cyan-light text-sm font-bold mt-1.5 drop-shadow">
                    Chief Executive Officer (CEO)
                  </p>
                  <p className="text-slate-300 text-xs font-medium drop-shadow">
                    Human Development & Leadership Consultant
                  </p>
                </div>
              </div>

              {/* Floating Bottom Holographic Ribbon in 3D Space */}
              <div
                style={{ transform: 'translateZ(60px)' }}
                className="mt-3 p-3 rounded-2xl bg-white/[0.05] border border-white/10 backdrop-blur-md flex items-center justify-between gap-2"
              >
                <div className="flex items-center gap-2 text-xs">
                  <span className="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse" />
                  <span className="text-slate-200 font-semibold">15+ Yrs Authority</span>
                </div>
                <div className="text-xs font-display font-bold text-cyan">
                  50,000+ Leaders Trained
                </div>
              </div>
            </motion.div>
          </div>
        </div>

        {/* 4 Bottom Animated Metric Cards across section */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mt-16 pt-12 border-t border-white/10 relative z-10">
          {impactMetrics.map((metric, mIdx) => {
            const Icon = metric.icon;
            return (
              <motion.div
                key={mIdx}
                whileHover={{ y: -5, borderColor: '#0099FF' }}
                className="p-5 rounded-2xl bg-white/[0.03] hover:bg-white/[0.07] border border-white/10 transition-all duration-300 shadow-md group"
              >
                <Icon className="w-6 h-6 text-cyan mb-2.5 group-hover:scale-110 transition-transform" />
                <div className="font-display text-2xl sm:text-3xl font-black text-white mb-1 group-hover:text-cyan transition-colors">
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
