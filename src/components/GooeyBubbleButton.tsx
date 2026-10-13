import React, { useEffect, useRef, useState } from 'react';
import gsap from 'gsap';
import { SlowMo } from 'gsap/EasePack';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(SlowMo);
}

interface GooeyBubbleButtonProps {
  country: string;
  subtitle: string;
  icon: React.ComponentType<{ className?: string }>;
  align?: 'left' | 'right';
  onClick?: () => void;
  className?: string;
}

export const GooeyBubbleButton: React.FC<GooeyBubbleButtonProps> = ({
  country,
  subtitle,
  icon: Icon,
  align = 'left',
  onClick,
  className = '',
}) => {
  const containerRef = useRef<HTMLDivElement>(null);
  const effectContainerRef = useRef<HTMLDivElement>(null);
  const effectBtnRef = useRef<HTMLSpanElement>(null);
  const tl0Ref = useRef<HTMLSpanElement>(null);
  const tl1Ref = useRef<HTMLSpanElement>(null);
  const tl2Ref = useRef<HTMLSpanElement>(null);
  const br0Ref = useRef<HTMLSpanElement>(null);
  const br1Ref = useRef<HTMLSpanElement>(null);
  const br2Ref = useRef<HTMLSpanElement>(null);

  const timelineRef = useRef<gsap.core.Timeline | null>(null);
  const [isHovered, setIsHovered] = useState(false);

  useEffect(() => {
    if (
      !effectBtnRef.current ||
      !tl0Ref.current ||
      !tl1Ref.current ||
      !tl2Ref.current ||
      !br0Ref.current ||
      !br1Ref.current ||
      !br2Ref.current
    ) {
      return;
    }

    const circlesTopLeft = [tl0Ref.current, tl1Ref.current, tl2Ref.current];
    const circlesBottomRight = [br0Ref.current, br1Ref.current, br2Ref.current];
    const effectBtn = effectBtnRef.current;

    // Resolve SlowMo ease with safe fallback
    const slowEase = SlowMo?.ease ? SlowMo.ease.config(0.1, 0.7, false) : 'power2.out';

    // Initial state
    gsap.set(circlesTopLeft, { x: 0, y: 0, scale: 0.8, opacity: 0, rotation: -45 });
    gsap.set(circlesBottomRight, { x: 0, y: 0, scale: 0.8, opacity: 0, rotation: 45 });
    gsap.set(effectBtn, { scaleY: 1, scaleX: 1, opacity: 0 });

    // Timeline 1: Top-Left bubbles detachment & dispersion
    const tl1 = gsap.timeline();
    tl1.set(circlesTopLeft, { opacity: 0.95 });
    tl1.to(circlesTopLeft, { duration: 1.2, x: -28, y: -28, scaleY: 2, ease: slowEase });
    tl1.to(circlesTopLeft[0], { duration: 0.1, scale: 0.25, x: '+=6', y: '-=2' }, '-=0.3');
    tl1.to(circlesTopLeft[1], { duration: 0.1, scaleX: 1, scaleY: 0.8, x: '-=10', y: '-=7' }, '-=0.1');
    tl1.to(circlesTopLeft[2], { duration: 0.1, scale: 0.25, x: '-=15', y: '+=6' }, '-=0.1');
    tl1.to(circlesTopLeft[0], { duration: 0.9, scale: 0, x: '-=6', y: '-=16', opacity: 0 });
    tl1.to(circlesTopLeft[1], { duration: 0.9, scaleX: 0.35, scaleY: 0.35, x: '-=12', y: '-=12', opacity: 0 }, '-=0.9');
    tl1.to(circlesTopLeft[2], { duration: 0.9, scale: 0, x: '-=16', y: '+=6', opacity: 0 }, '-=0.9');

    const tlBt1 = gsap.timeline();
    tlBt1.set(circlesTopLeft, { x: 0, y: 0, rotation: -45, scale: 0.8, opacity: 0.95 });
    tlBt1.add(tl1);

    // Timeline 2: Bottom-Right bubbles detachment & dispersion
    const tl2 = gsap.timeline();
    tl2.set(circlesBottomRight, { x: 0, y: 0, opacity: 0.95 });
    tl2.to(circlesBottomRight, { duration: 1.1, x: 30, y: 30, ease: slowEase });
    tl2.to(circlesBottomRight[0], { duration: 0.1, scale: 0.25, x: '-=6', y: '+=3' }, '-=0.3');
    tl2.to(circlesBottomRight[1], { duration: 0.1, scale: 0.8, x: '+=7', y: '+=3' }, '-=0.1');
    tl2.to(circlesBottomRight[2], { duration: 0.1, scale: 0.25, x: '+=15', y: '-=6' }, '-=0.2');
    tl2.to(circlesBottomRight[0], { duration: 0.9, scale: 0, x: '+=6', y: '+=16', opacity: 0 });
    tl2.to(circlesBottomRight[1], { duration: 0.9, scale: 0.35, x: '+=8', y: '+=8', opacity: 0 }, '-=0.9');
    tl2.to(circlesBottomRight[2], { duration: 0.9, scale: 0, x: '+=16', y: '-=6', opacity: 0 }, '-=0.9');

    const tlBt2 = gsap.timeline();
    tlBt2.set(circlesBottomRight, { x: 0, y: 0, rotation: 45, scale: 0.8, opacity: 0.95 });
    tlBt2.add(tl2);

    // Master Timeline: Combining fluid bubbles + elastic jelly bounce
    const btTl = gsap.timeline({
      paused: true,
      onComplete: () => {
        gsap.to(effectBtn, { opacity: 0, duration: 0.3 });
      },
    });

    btTl.set(effectBtn, { opacity: 0.5 });
    btTl.add(tlBt1);
    btTl.to(effectBtn, { duration: 0.8, scaleY: 1.15, scaleX: 1.05 }, 0.1);
    btTl.add(tlBt2, 0.2);
    btTl.to(effectBtn, { duration: 1.8, scale: 1, ease: 'elastic.out(1.2, 0.4)' }, 1.2);

    btTl.timeScale(2.5);
    timelineRef.current = btTl;

    return () => {
      btTl.kill();
    };
  }, []);

  const handleMouseEnter = () => {
    setIsHovered(true);
    timelineRef.current?.restart();
  };

  const handleMouseLeave = () => {
    setIsHovered(false);
  };

  const handleTouchStart = () => {
    setIsHovered(true);
    timelineRef.current?.restart();
  };

  const handleTouchEnd = () => {
    setTimeout(() => {
      setIsHovered(false);
    }, 1200);
  };

  return (
    <div
      ref={containerRef}
      className={`button--bubble__container relative inline-block group pointer-events-auto touch-manipulation ${className}`}
      onMouseEnter={handleMouseEnter}
      onMouseLeave={handleMouseLeave}
      onTouchStart={handleTouchStart}
      onTouchEnd={handleTouchEnd}
    >
      {/* 1. Behind: Gooey Liquid SVG Effect Container */}
      <div
        ref={effectContainerRef}
        className={`button--bubble__effect-container transition-opacity duration-300 pointer-events-none ${
          isHovered ? 'opacity-100' : 'opacity-0'
        }`}
      >
        {/* Top-Left Liquid Droplet Bubbles */}
        <span ref={tl0Ref} className="circle top-left" />
        <span ref={tl1Ref} className="circle top-left" />
        <span ref={tl2Ref} className="circle top-left" />

        {/* Elastic Liquid Rebound Core (Behind Button) */}
        <span ref={effectBtnRef} className="effect-button" />

        {/* Bottom-Right Liquid Droplet Bubbles */}
        <span ref={br0Ref} className="circle bottom-right" />
        <span ref={br1Ref} className="circle bottom-right" />
        <span ref={br2Ref} className="circle bottom-right" />
      </div>

      {/* 2. In Front: 100% Fully Transparent Glass Button with Ramotion Spin Disc */}
      <button
        type="button"
        onClick={onClick}
        className="button--bubble relative z-10 flex items-center gap-2 sm:gap-3.5 px-2.5 sm:px-4 py-1.5 sm:py-2.5 rounded-full bg-white/5 hover:bg-white/15 active:bg-white/20 backdrop-blur-md border border-white/20 hover:border-cyan/90 shadow-[0_8px_30px_rgba(0,153,255,0.08)] hover:shadow-[0_0_30px_rgba(0,210,255,0.55)] transition-all duration-300 cursor-pointer focus:outline-none"
      >
        {align === 'left' && (
          <div className="relative w-8 h-8 sm:w-11 sm:h-11 rounded-full flex items-center justify-center bg-gradient-to-br from-[#0B2545]/85 via-navy/60 to-cyan/85 border border-white/30 shadow-md group-hover:border-cyan group-hover:shadow-[0_0_22px_rgba(0,210,255,0.9)] transition-all duration-500 flex-shrink-0">
            <span className="absolute inset-0 rounded-full border border-cyan/40 animate-ping opacity-40 pointer-events-none" />
            <span className="absolute -inset-1 rounded-full border border-cyan/30 opacity-0 group-hover:opacity-100 group-hover:scale-125 transition-all duration-500 pointer-events-none" />
            <Icon className="w-4 h-4 sm:w-5 sm:h-5 text-white group-hover:rotate-[360deg] transition-transform duration-700 ease-out" />
          </div>
        )}

        <div className={align === 'right' ? 'text-right' : 'text-left'}>
          <div
            className="text-xs sm:text-base lg:text-lg font-display font-black tracking-wider sm:tracking-widest text-[#07172C] group-hover:text-cyan transition-colors leading-tight"
            style={{ textShadow: '0 0 16px rgba(255,255,255,1), 0 1px 2px rgba(255,255,255,0.9)' }}
          >
            {country}
          </div>
          <div className="text-[8px] sm:text-[10px] font-extrabold text-[#0B2545]/85 uppercase tracking-wider hidden xs:block sm:block leading-tight">
            {subtitle}
          </div>
        </div>

        {align === 'right' && (
          <div className="relative w-8 h-8 sm:w-11 sm:h-11 rounded-full flex items-center justify-center bg-gradient-to-br from-[#0B2545]/85 via-navy/60 to-cyan/85 border border-white/30 shadow-md group-hover:border-cyan group-hover:shadow-[0_0_22px_rgba(0,210,255,0.9)] transition-all duration-500 flex-shrink-0">
            <span className="absolute inset-0 rounded-full border border-cyan/40 animate-ping opacity-40 pointer-events-none" />
            <span className="absolute -inset-1 rounded-full border border-cyan/30 opacity-0 group-hover:opacity-100 group-hover:scale-125 transition-all duration-500 pointer-events-none" />
            <Icon className="w-4 h-4 sm:w-5 sm:h-5 text-white group-hover:rotate-[360deg] transition-transform duration-700 ease-out" />
          </div>
        )}
      </button>
    </div>
  );
};
