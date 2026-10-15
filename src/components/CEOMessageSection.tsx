import React from 'react';
import { motion } from 'framer-motion';
import { Sparkles, ArrowRight, GraduationCap } from 'lucide-react';

interface CEOMessageSectionProps {
  onOpenConsultation?: () => void;
}

export const CEOMessageSection: React.FC<CEOMessageSectionProps> = ({ onOpenConsultation }) => {
  return (
    <section
      className="py-20 md:py-28 bg-gradient-to-br from-[#040E1B] via-[#071A36] to-[#0A264E] relative overflow-hidden text-white border-y border-cyan/20"
      id="ceo-message"
    >
      {/* Ambient Radial Spotlight (Exact Warren Buffet Poster Backlight Effect) */}
      <div className="absolute top-1/4 right-1/4 w-[38rem] h-[38rem] bg-cyan/20 rounded-full blur-[160px] pointer-events-none" />
      <div className="absolute bottom-10 left-10 w-[30rem] h-[30rem] bg-blue-600/15 rounded-full blur-[160px] pointer-events-none" />

      {/* Subtle Geometric Overlay */}
      <div
        className="absolute inset-0 opacity-[0.03] pointer-events-none"
        style={{
          backgroundImage: `radial-gradient(circle at 1.5px 1.5px, #0099FF 1px, transparent 0)`,
          backgroundSize: '40px 40px'
        }}
      />

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* MAIN POSTER HERO GRID (Exact Warren Buffet Poster Structure) */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center min-h-[640px]">
          {/* LEFT COLUMN: Quote, Thin Geometric Line, Narrative, and Credentials */}
          <div className="lg:col-span-6 flex flex-col justify-center relative z-20">
            {/* Top Creator / CEO Badge (Matching Top-Left Avatar in Reference) */}
            <motion.div
              initial={{ opacity: 0, y: 15 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              className="flex items-center gap-3 mb-6"
            >
              <div className="w-11 h-11 rounded-full overflow-hidden border-2 border-cyan shadow-[0_0_15px_rgba(0,153,255,0.4)] bg-navy-dark shrink-0">
                <img
                  src="/assets/images/team/muhammad-nawaz.jpg"
                  alt="Muhammad Nawaz"
                  className="w-full h-full object-cover object-[28%_15%] scale-[1.7]"
                />
              </div>
              <div>
                <div className="font-display font-extrabold text-sm text-white tracking-wide">
                  Muhammad Nawaz
                </div>
                <div className="text-cyan text-xs font-semibold tracking-wider uppercase">
                  Chief Executive Officer
                </div>
              </div>
            </motion.div>

            {/* The Big Iconic Statement (Matching Typography & Accent of Warren Buffet Quote) */}
            <motion.div
              initial={{ opacity: 0, y: 15 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: 0.1 }}
              className="space-y-4 mb-6"
            >
              <h2 className="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight tracking-tight">
                "Converting <span className="text-cyan font-black">Human Potential</span> into{' '}
                <span className="text-cyan font-black">Human Capital</span>."
              </h2>

              <p className="text-slate-200 text-base sm:text-lg lg:text-xl font-normal leading-relaxed">
                It takes <strong>15 years</strong> of dedication to build a foundation of excellence, and{' '}
                <strong>unwavering purpose</strong> to empower the next generation.
              </p>

              <p className="text-cyan-light text-base sm:text-lg font-medium leading-relaxed">
                If you empower the mindset of youth, <strong>you transform the entire future.</strong>
              </p>
            </motion.div>

            {/* Thin Geometric Connecting Line (Signature Warren Buffet Poster Detail) */}
            <div className="relative mb-6 hidden sm:block">
              <div className="w-44 h-10 border-l-2 border-b-2 border-cyan/40" />
            </div>

            {/* CEO Narrative Text */}
            <motion.div
              initial={{ opacity: 0, y: 15 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: 0.2 }}
              className="space-y-3.5 text-slate-300 text-sm sm:text-base leading-relaxed mb-6"
            >
              <p>
                <strong className="text-white font-semibold">Muhammad Nawaz</strong> is a leadership and human development professional with over{' '}
                <span className="text-cyan-light font-bold">15 years of proven experience</span> in personal development, leadership training, human performance, and youth development in Pakistan.
              </p>

              <p className="text-xs sm:text-sm text-slate-400">
                At Gen Z Consultant, he bridges the gap between education, personal development, and career success — empowering scholars with the mindset and capabilities to thrive internationally.
              </p>
            </motion.div>

            {/* Academic Qualifications */}
            <motion.div
              initial={{ opacity: 0, y: 15 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: 0.25 }}
              className="flex flex-wrap items-center gap-2 mb-8"
            >
              <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-cyan/15 border border-cyan/30 text-cyan-light text-xs font-semibold">
                <GraduationCap className="w-3.5 h-3.5 text-cyan" />
                <span>MSc Waste Management & Environmental Sciences (UK)</span>
              </span>
              <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-slate-200 text-xs font-semibold">
                <GraduationCap className="w-3.5 h-3.5 text-slate-300" />
                <span>MSc Geography (University of the Punjab)</span>
              </span>
            </motion.div>

            {/* Action CTA Button */}
            <motion.div
              initial={{ opacity: 0, y: 15 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: 0.3 }}
            >
              <button
                onClick={onOpenConsultation}
                className="group relative px-8 py-4 rounded-xl bg-gradient-to-r from-cyan via-blue-500 to-cyan bg-[length:200%_auto] hover:bg-right font-display text-sm font-bold text-white shadow-[0_0_30px_rgba(0,153,255,0.45)] hover:shadow-[0_0_45px_rgba(0,153,255,0.7)] transition-all duration-500 hover:scale-105 active:scale-95 flex items-center gap-3"
              >
                <Sparkles className="w-4 h-4 text-white" />
                <span>Connect with CEO & Mentorship Office</span>
                <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
              </button>
            </motion.div>
          </div>

          {/* RIGHT COLUMN: Seamless Cinematic Portrait (Exact Warren Buffet Poster Placement) */}
          <div className="lg:col-span-6 relative flex justify-center lg:justify-end items-end h-[520px] sm:h-[620px] lg:h-[680px]">
            {/* Ambient Cyan Radial Rim Light behind Head */}
            <div className="absolute top-10 right-10 sm:right-20 w-80 h-80 bg-gradient-to-br from-cyan/35 to-blue-600/25 rounded-full blur-[90px] pointer-events-none" />

            {/* Heroic Portrait Blended Seamlessly into Canvas (NO BOXES, NO PHONES!) */}
            <div className="relative w-full max-w-[480px] h-full overflow-hidden">
              <img
                src="/assets/images/team/muhammad-nawaz.jpg"
                alt="Muhammad Nawaz - CEO Gen Z Consultant"
                className="w-full h-full object-cover object-[28%_15%] scale-[1.38] filter brightness-105 contrast-105"
              />

              {/* Edge Gradient Fades for Seamless Canvas Integration */}
              {/* Left Edge Fade */}
              <div className="absolute inset-y-0 left-0 w-32 bg-gradient-to-r from-[#040E1B] via-[#040E1B]/80 to-transparent pointer-events-none" />
              {/* Bottom Edge Fade */}
              <div className="absolute inset-x-0 bottom-0 h-48 bg-gradient-to-t from-[#040E1B] via-[#071A36]/90 to-transparent pointer-events-none" />
              {/* Top Edge Fade */}
              <div className="absolute inset-x-0 top-0 h-24 bg-gradient-to-b from-[#040E1B]/80 to-transparent pointer-events-none" />

              {/* Huge Bold Typography Overlaid on Bottom Right (Exact Warren Buffet Placement) */}
              <div className="absolute bottom-6 sm:bottom-10 right-4 sm:right-8 z-30 text-right">
                <h3 className="font-display text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-[0.92] drop-shadow-[0_4px_15px_rgba(0,0,0,0.8)]">
                  Muhammad
                  <br />
                  Nawaz
                </h3>
                <p className="text-cyan text-sm sm:text-base font-extrabold mt-3 tracking-wide drop-shadow-[0_2px_8px_rgba(0,0,0,0.8)]">
                  Chief Executive Officer (CEO)
                </p>
                <p className="text-slate-300 text-xs sm:text-sm font-medium tracking-wider drop-shadow-[0_2px_8px_rgba(0,0,0,0.8)]">
                  Human Development & Leadership Consultant
                </p>
              </div>
            </div>
          </div>
        </div>

        {/* Minimalist Bottom Impact Numbers Strip (Clean & Non-Cluttered) */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-6 pt-10 mt-12 border-t border-white/10 relative z-10">
          <div className="text-center md:text-left">
            <div className="font-display text-3xl sm:text-4xl font-black text-cyan">50,000+</div>
            <div className="text-xs font-semibold text-slate-300 mt-1">Individuals Trained</div>
            <div className="text-[11px] text-slate-500">Across Leadership Programs</div>
          </div>

          <div className="text-center md:text-left">
            <div className="font-display text-3xl sm:text-4xl font-black text-white">15+ Years</div>
            <div className="text-xs font-semibold text-slate-300 mt-1">Executive Leadership</div>
            <div className="text-[11px] text-slate-500">Proven Human Performance Track Record</div>
          </div>

          <div className="text-center md:text-left">
            <div className="font-display text-3xl sm:text-4xl font-black text-cyan">1,500+</div>
            <div className="text-xs font-semibold text-slate-300 mt-1">Life Mastery Scholars</div>
            <div className="text-[11px] text-slate-500">3-Day Immersive Retreats</div>
          </div>

          <div className="text-center md:text-left">
            <div className="font-display text-3xl sm:text-4xl font-black text-white">1,000+</div>
            <div className="text-xs font-semibold text-slate-300 mt-1">Youth Leaders Camp</div>
            <div className="text-[11px] text-slate-500">Summer Camps & Youth Retreats</div>
          </div>
        </div>
      </div>
    </section>
  );
};
