import React, { useEffect, useRef } from 'react';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { Sparkles, ArrowRight } from 'lucide-react';

gsap.registerPlugin(ScrollTrigger);

interface CampusLifeProps {
  onOpenModal: () => void;
}

export const CampusLifeSection: React.FC<CampusLifeProps> = ({ onOpenModal }) => {
  const sectionRef = useRef<HTMLDivElement>(null);
  const bellContainerRef = useRef<HTMLDivElement>(null);
  const bellRef = useRef<HTMLDivElement>(null);
  const clapperRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (!sectionRef.current || !bellRef.current || !clapperRef.current) return;

    // Set initial transform origin at top center for realistic pendulum physics
    gsap.set(bellRef.current, { rotation: -16, transformOrigin: 'top center' });
    gsap.set(clapperRef.current, { rotation: 22, transformOrigin: 'top center' });

    // Master Timeline for continuous smooth swinging bell
    const master = gsap.timeline({ repeat: -1, paused: true });

    const bellTl = gsap.timeline();
    bellTl
      .to(bellRef.current, { rotation: 16, duration: 1.3, ease: 'sine.inOut' })
      .to(bellRef.current, { rotation: -16, duration: 1.3, ease: 'sine.inOut' });

    const clapperTl = gsap.timeline();
    clapperTl
      .to(clapperRef.current, { rotation: -22, duration: 1.3, ease: 'sine.inOut' })
      .to(clapperRef.current, { rotation: 22, duration: 1.3, ease: 'sine.inOut' });

    master.add(bellTl, 0).add(clapperTl, 0.08);

    // ScrollTrigger to play pendulum when in viewport + add scroll velocity reaction
    const trigger = ScrollTrigger.create({
      trigger: sectionRef.current,
      start: 'top 85%',
      end: 'bottom 15%',
      onToggle: (self) => {
        if (self.isActive) master.play();
        else master.pause();
      },
      onUpdate: (self) => {
        // Add subtle tilt impulse on scroll velocity
        if (bellContainerRef.current) {
          const velocity = self.getVelocity() / 350;
          const clampedVelocity = Math.max(-12, Math.min(12, velocity));
          gsap.to(bellContainerRef.current, {
            rotation: clampedVelocity,
            duration: 0.4,
            ease: 'power1.out',
            overwrite: 'auto',
          });
        }
      },
    });

    return () => {
      trigger.kill();
      master.kill();
    };
  }, []);

  return (
    <section
      ref={sectionRef}
      className="py-24 sm:py-32 bg-[#FAF7F2] relative overflow-hidden"
      id="campus"
    >
      {/* Top Cloud Decorative Transition */}
      <div className="absolute top-0 left-0 right-0 h-16 pointer-events-none opacity-40 z-0">
        <img
          src="/assets/images/cream-top-cloud.webp"
          alt=""
          className="w-full h-full object-cover"
        />
      </div>

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Section Tag & Editorial Headline */}
        <div className="text-center max-w-3xl mx-auto mb-16 sm:mb-20">
          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/10 border border-cyan/20 text-cyan text-xs font-display font-bold uppercase tracking-wider mb-4">
            <Sparkles className="w-3.5 h-3.5" />
            <span>( Life at GenZ )</span>
          </div>
          <h2 className="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-navy tracking-tight leading-[1.1] mb-6">
            Life Beyond the Classroom<span className="text-cyan animate-pulse">|</span>
          </h2>
          <p className="text-slate-600 text-base sm:text-lg max-w-2xl mx-auto">
            Purpose-built modern environments designed to cultivate resilience, creative expression, athletic stamina, and student leadership.
          </p>
        </div>

        {/* Center Swinging Bell & 4 Illustrated Stations */}
        <div className="relative max-w-6xl mx-auto min-h-[640px] flex items-center justify-center">
          {/* Top Hanging Mount & Big Swinging Bell */}
          <div
            ref={bellContainerRef}
            className="absolute top-0 left-1/2 -translate-x-1/2 flex flex-col items-center pointer-events-none z-20"
          >
            {/* Ceiling Bracket */}
            <div className="w-10 h-3 rounded-b-md bg-gradient-to-r from-cyan via-blue-500 to-purple-500 shadow-[0_0_12px_rgba(0,240,255,0.75)]" />

            {/* Hanging Cord with Animated Neon Energy Flow */}
            <div className="w-[3px] h-20 sm:h-28 animate-neon-cord rounded-full" />

            {/* Big School Bell Assembly (Vibrant Animated Neon Color-Changing Bell) */}
            <div
              ref={bellRef}
              className="relative w-56 sm:w-72 md:w-80 h-56 sm:h-72 md:h-80 -mt-2"
            >
              {/* Dynamic Neon Color-Changing Bell Body */}
              <img
                src="/assets/images/ring.webp"
                alt="GenZ Animated Neon School Bell"
                className="w-full h-full object-contain animate-neon-bell"
              />

              {/* Clapper / Bell Tongue with Synchronized Neon Animation */}
              <div
                ref={clapperRef}
                className="absolute bottom-6 sm:bottom-8 left-1/2 -translate-x-1/2 w-14 sm:w-16 h-14 sm:h-16"
              >
                <img
                  src="/assets/images/ring-bell.webp"
                  alt=""
                  className="w-full h-full object-contain animate-neon-bell"
                />
              </div>

              {/* Multi-Layered Expanding Neon Acoustic Energy Ripples */}
              <div className="absolute bottom-10 left-1/2 w-28 h-28 rounded-full border-2 animate-neon-ripple-1 pointer-events-none" />
              <div className="absolute bottom-10 left-1/2 w-28 h-28 rounded-full border-2 animate-neon-ripple-2 pointer-events-none" />
            </div>
          </div>

          {/* 4 Illustrated Stations Around the Bell with generous horizontal spacing */}
          <div className="w-full grid grid-cols-1 md:grid-cols-2 gap-y-16 md:gap-y-24 gap-x-20 lg:gap-x-48 relative z-10 pt-48 md:pt-8">
            {/* Top Left: Campus & Facilities */}
            <div
              onClick={onOpenModal}
              className="cursor-pointer group flex flex-col items-start max-w-sm bg-white/60 hover:bg-white p-6 sm:p-7 rounded-3xl border border-navy/5 hover:border-cyan/30 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
            >
              <div className="w-20 h-20 mb-4 transform group-hover:scale-110 group-hover:-rotate-6 transition-all duration-300">
                <img
                  src="/assets/images/lal-card-icon-1.webp"
                  alt="Campus & Facilities"
                  className="w-full h-full object-contain"
                />
              </div>
              <div className="text-xs font-bold text-cyan uppercase tracking-wider mb-1">Infrastructure</div>
              <h3 className="font-display text-2xl font-bold text-navy mb-2 group-hover:text-cyan transition-colors flex items-center justify-between w-full">
                <span>Campus & Facilities</span>
                <ArrowRight className="w-4 h-4 text-cyan opacity-0 group-hover:opacity-100 transform translate-x-0 group-hover:translate-x-1 transition-all" />
              </h3>
              <p className="text-slate-600 text-sm leading-relaxed">
                Modern learning spaces, world-class sports facilities, performing arts venues, and purpose-built environments designed to inspire student growth.
              </p>
            </div>

            {/* Top Right: Sports & Athletics */}
            <div
              onClick={onOpenModal}
              className="cursor-pointer group flex flex-col items-start md:items-end md:text-right max-w-sm ml-auto bg-white/60 hover:bg-white p-6 sm:p-7 rounded-3xl border border-navy/5 hover:border-cyan/30 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
            >
              <div className="w-20 h-20 mb-4 transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                <img
                  src="/assets/images/lal-card-icon-3.webp"
                  alt="Sports"
                  className="w-full h-full object-contain"
                />
              </div>
              <div className="text-xs font-bold text-cyan uppercase tracking-wider mb-1">Athletics & Stamina</div>
              <h3 className="font-display text-2xl font-bold text-navy mb-2 group-hover:text-cyan transition-colors flex items-center justify-between md:justify-end gap-2 w-full">
                <span>Sports & Athletics</span>
                <ArrowRight className="w-4 h-4 text-cyan opacity-0 group-hover:opacity-100 transform translate-x-0 group-hover:translate-x-1 transition-all" />
              </h3>
              <p className="text-slate-600 text-sm leading-relaxed">
                From swimming and futsal to gymnastics, badminton, and taekwondo, students build resilience, teamwork, and confidence through competitive sports.
              </p>
            </div>

            {/* Bottom Left: Societies & MUN */}
            <div
              onClick={onOpenModal}
              className="cursor-pointer group flex flex-col items-start max-w-sm mt-4 sm:mt-10 bg-white/60 hover:bg-white p-6 sm:p-7 rounded-3xl border border-navy/5 hover:border-cyan/30 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
            >
              <div className="w-20 h-20 mb-4 transform group-hover:scale-110 group-hover:-rotate-6 transition-all duration-300">
                <img
                  src="/assets/images/lal-card-icon-2.webp"
                  alt="Societies"
                  className="w-full h-full object-contain"
                />
              </div>
              <div className="text-xs font-bold text-cyan uppercase tracking-wider mb-1">Global Citizenship</div>
              <h3 className="font-display text-2xl font-bold text-navy mb-2 group-hover:text-cyan transition-colors flex items-center justify-between w-full">
                <span>Student Societies & MUN</span>
                <ArrowRight className="w-4 h-4 text-cyan opacity-0 group-hover:opacity-100 transform translate-x-0 group-hover:translate-x-1 transition-all" />
              </h3>
              <p className="text-slate-600 text-sm leading-relaxed">
                Student societies foster collaboration, creativity, communication, and real-world engagement through meaningful debates, delegations, and competitions.
              </p>
            </div>

            {/* Bottom Right: Co-curricular Activities */}
            <div
              onClick={onOpenModal}
              className="cursor-pointer group flex flex-col items-start md:items-end md:text-right max-w-sm ml-auto mt-4 sm:mt-10 bg-white/60 hover:bg-white p-6 sm:p-7 rounded-3xl border border-navy/5 hover:border-cyan/30 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
            >
              <div className="w-20 h-20 mb-4 transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                <img
                  src="/assets/images/lal-card-icon-4.webp"
                  alt="Co-curricular Activities"
                  className="w-full h-full object-contain"
                />
              </div>
              <div className="text-xs font-bold text-cyan uppercase tracking-wider mb-1">Creativity & Innovation</div>
              <h3 className="font-display text-2xl font-bold text-navy mb-2 group-hover:text-cyan transition-colors flex items-center justify-between md:justify-end gap-2 w-full">
                <span>Co-curricular Activities</span>
                <ArrowRight className="w-4 h-4 text-cyan opacity-0 group-hover:opacity-100 transform translate-x-0 group-hover:translate-x-1 transition-all" />
              </h3>
              <p className="text-slate-600 text-sm leading-relaxed">
                Students engage in performing arts, robotics, public speaking, debates, and leadership opportunities beyond the traditional classroom.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};
