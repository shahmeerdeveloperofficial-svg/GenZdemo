import { getContent, siteAsset, safeUrl, route, siteConfig } from '../content';
import React, { useEffect, useRef } from 'react';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { Sparkles, ArrowRight, BellRing, Radio } from 'lucide-react';

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
    if (!sectionRef.current || !bellRef.current || !clapperRef.current || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

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

    // High-performance quickTo setter avoids GC churn and tween rebuilds during scrolling
    const tiltSetter = bellContainerRef.current
      ? gsap.quickTo(bellContainerRef.current, 'rotation', { duration: 0.4, ease: 'power1.out' })
      : null;

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
        if (tiltSetter) {
          const velocity = self.getVelocity() / 350;
          const clampedVelocity = Math.max(-12, Math.min(12, velocity));
          tiltSetter(clampedVelocity);
        }
      },
    });

    return () => {
      trigger.kill();
      master.kill();
    };
  }, []);

  const defaultStations = [
    {
      image: '/assets/images/lal-card-icon-1.webp',
      title: 'Campus & Facilities',
      category: 'Infrastructure',
      alertStatus: 'LIVE STATUS: OPEN FOR TOURS',
      description: 'Modern learning spaces, world-class sports facilities, performing arts venues, and purpose-built environments designed to inspire student growth.'
    },
    {
      image: '/assets/images/lal-card-icon-3.webp',
      title: 'Sports & Athletics',
      category: 'Athletics & Stamina',
      alertStatus: 'FACILITY ALERT: EXPANDED',
      description: 'From swimming and futsal to gymnastics, badminton, and taekwondo, students build resilience, teamwork, and confidence through competitive sports.'
    },
    {
      image: '/assets/images/lal-card-icon-2.webp',
      title: 'Student Societies & MUN',
      category: 'Global Citizenship',
      alertStatus: 'SOCIETY BULLETIN: ACTIVE',
      description: 'Student societies foster collaboration, creativity, communication, and real-world engagement through meaningful debates, delegations, and competitions.'
    },
    {
      image: '/assets/images/lal-card-icon-4.webp',
      title: 'Co-curricular Activities',
      category: 'Creativity & Innovation',
      alertStatus: 'NEW PROGRAM: ENROLLING',
      description: 'Students engage in performing arts, robotics, public speaking, debates, and leadership opportunities beyond the traditional classroom.'
    }
  ];

  const stations = getContent<typeof defaultStations>('sections.campus.stations', defaultStations);

  return (
    <section
      ref={sectionRef}
      className="py-24 sm:py-32 bg-[#FAF7F2] relative overflow-hidden"
      id="campus"
    >
      {/* Top Cloud Decorative Transition */}
      <div className="absolute top-0 left-0 right-0 h-16 pointer-events-none opacity-40 z-0">
        <img
          loading="lazy"
          decoding="async"
          src={siteAsset(getContent<string>("sections.campus.media.cream_top_cloud_webp", "/assets/images/cream-top-cloud.webp"))}
          alt=""
          className="w-full h-full object-cover"
        />
      </div>

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Section Tag & Editorial Headline */}
        <div className="text-center max-w-3xl mx-auto mb-16 sm:mb-20">
          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/15 border border-cyan/30 text-cyan text-xs font-display font-bold uppercase tracking-wider mb-4 shadow-sm">
            <Radio className="w-3.5 h-3.5 text-cyan animate-pulse" />
            <span>{getContent<string>("sections.campus.copy.lifeAtGenz", "( Life at GenZ )")}</span>
          </div>
          <h2 className="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-navy tracking-tight leading-[1.1] mb-6">
            {getContent<string>("sections.campus.copy.lifeBeyondTheClassroom", "Life Beyond the Classroom")}
            <span className="text-cyan animate-pulse">{getContent<string>("sections.campus.copy.label", "|")}</span>
          </h2>
          <p className="text-slate-600 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
            {getContent<string>("sections.campus.copy.purposeBuiltModernEnvironmentsDesignedTo", "Purpose-built modern environments designed to cultivate resilience, creative expression, athletic stamina, and student leadership.")}
          </p>
        </div>

        {/* Center Swinging Bell & 4 Alert-Styled Campus Station Cards */}
        <div className="relative max-w-6xl mx-auto min-h-[660px] flex items-center justify-center">
          {/* Top Hanging Mount & Big Swinging Bell (PRESERVED UNCHANGED) */}
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
                loading="lazy"
                decoding="async"
                src={siteAsset(getContent<string>("sections.campus.media.ring_webp", "/assets/images/ring.webp"))}
                alt={getContent<string>("sections.campus.copy.genzAnimatedNeonSchoolBell", "GenZ Animated Neon School Bell")}
                className="w-full h-full object-contain animate-neon-bell"
              />

              {/* Clapper / Bell Tongue with Synchronized Neon Animation */}
              <div
                ref={clapperRef}
                className="absolute bottom-6 sm:bottom-8 left-1/2 -translate-x-1/2 w-14 sm:w-16 h-14 sm:h-16"
              >
                <img
                  loading="lazy"
                  decoding="async"
                  src={siteAsset(getContent<string>("sections.campus.media.ring_bell_webp", "/assets/images/ring-bell.webp"))}
                  alt=""
                  className="w-full h-full object-contain animate-neon-bell"
                />
              </div>

              {/* Multi-Layered Expanding Neon Acoustic Energy Ripples */}
              <div className="absolute bottom-10 left-1/2 w-28 h-28 rounded-full border-2 animate-neon-ripple-1 pointer-events-none" />
              <div className="absolute bottom-10 left-1/2 w-28 h-28 rounded-full border-2 animate-neon-ripple-2 pointer-events-none" />
            </div>
          </div>

          {/* 4 Alert-Styled Campus Broadcast Cards Around the Bell */}
          <div className="w-full grid grid-cols-1 md:grid-cols-2 gap-y-12 md:gap-y-20 gap-x-16 lg:gap-x-44 relative z-10 pt-52 md:pt-8">
            {stations.map((station, index) => {
              const alertStatus = station.alertStatus || 'CAMPUS LIVE ALERT';
              return (
                <button
                  type="button"
                  key={index}
                  onClick={onOpenModal}
                  className={`text-left cursor-pointer group flex flex-col max-w-sm sm:max-w-md w-full bg-white/95 backdrop-blur-md p-6 sm:p-7 rounded-3xl border-2 border-slate-200/90 shadow-[0_12px_35px_rgba(11,37,69,0.08)] hover:shadow-[0_22px_50px_rgba(0,153,255,0.22)] hover:border-cyan hover:-translate-y-2 transition-all duration-300 relative overflow-hidden ${
                    index % 2 ? 'md:ml-auto' : ''
                  }`}
                >
                  {/* Left Alert Glow Accent Bar */}
                  <div className="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-cyan via-blue-500 to-purple-500 group-hover:w-2.5 transition-all duration-300" />

                  {/* Top Alert / Live Broadcast Header */}
                  <div className="flex items-center justify-between gap-3 mb-4 w-full">
                    <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan/15 border border-cyan/30 text-cyan-dark text-[11px] font-display font-extrabold uppercase tracking-wider shadow-2xs">
                      <span className="relative flex h-2 w-2">
                        <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan opacity-75"></span>
                        <span className="relative inline-flex rounded-full h-2 w-2 bg-cyan"></span>
                      </span>
                      <span>{station.category}</span>
                    </div>

                    <span className="text-[10px] font-extrabold uppercase tracking-widest px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 group-hover:bg-cyan/10 group-hover:text-cyan transition-colors">
                      {alertStatus.split(':')[0]}
                    </span>
                  </div>

                  {/* Icon & Title Row */}
                  <div className="flex items-start gap-4 mb-3">
                    <div className="w-16 h-16 rounded-2xl bg-gradient-to-br from-slate-50 to-cyan/10 border-2 border-slate-200/80 p-2.5 flex items-center justify-center shrink-0 shadow-sm group-hover:scale-110 group-hover:border-cyan/50 group-hover:shadow-[0_8px_20px_rgba(0,153,255,0.25)] transition-all duration-300">
                      <img
                        src={siteAsset(station.image)}
                        alt={station.title}
                        className="w-full h-full object-contain filter drop-shadow-sm"
                        loading="lazy"
                      />
                    </div>
                    <div>
                      <h3 className="font-display text-xl sm:text-2xl font-black text-navy group-hover:text-cyan transition-colors leading-tight">
                        {station.title}
                      </h3>
                      <p className="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-1">
                        Verified Facility & Program
                      </p>
                    </div>
                  </div>

                  {/* Description */}
                  <p className="text-slate-600 text-xs sm:text-sm leading-relaxed mb-5">
                    {station.description}
                  </p>

                  {/* Bottom Interactive Alert Action */}
                  <div className="mt-auto pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-display font-bold text-navy group-hover:text-cyan transition-colors w-full">
                    <span className="flex items-center gap-1.5">
                      <BellRing className="w-3.5 h-3.5 text-cyan" />
                      <span>Explore Facility Details</span>
                    </span>
                    <div className="w-8 h-8 rounded-full bg-cyan/10 group-hover:bg-cyan group-hover:text-white flex items-center justify-center transition-all duration-300 shadow-2xs group-hover:scale-110">
                      <ArrowRight className="w-4 h-4" />
                    </div>
                  </div>
                </button>
              );
            })}
          </div>
        </div>
      </div>
    </section>
  );
};
