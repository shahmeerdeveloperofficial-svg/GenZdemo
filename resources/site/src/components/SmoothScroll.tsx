import React, { useEffect } from 'react';
import Lenis from 'lenis';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

// Global reference for programmatic scrolling
declare global {
  interface Window {
    __lenis?: Lenis;
  }
}

export const SmoothScroll: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  useEffect(() => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const touchDevice = window.matchMedia('(pointer: coarse), (max-width: 767px)').matches;
    // Keep native touch scrolling on phones and tablets; it responds immediately
    // to finger movement and lets the browser use its own momentum scrolling.
    if (reducedMotion || touchDevice) {
      const handleNativeAnchors = (e: MouseEvent) => {
        if (e.defaultPrevented || e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || !(e.target instanceof Element)) return;
        const anchor = (e.target as HTMLElement).closest('a[href^="#"]');
        const href = anchor?.getAttribute('href');
        if (!href || href.length < 2) return;
        let target: HTMLElement | null = null;
        try { target = document.getElementById(decodeURIComponent(href.slice(1))); } catch { return; }
        if (!target) return;
        e.preventDefault();
        target.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'start' });
      };
      document.addEventListener('click', handleNativeAnchors);
      return () => document.removeEventListener('click', handleNativeAnchors);
    }
    // Desktop uses Lenis, synchronized with GSAP's scroll effects.
    const lenis = new Lenis({
      duration: 0.85, // Snappy & responsive (eliminates heavy/sponge-like delay)
      easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
      orientation: 'vertical',
      gestureOrientation: 'vertical',
      smoothWheel: true,
      wheelMultiplier: 1.0,
      touchMultiplier: 1.5,
      syncTouch: false, // Ensures mobile touch uses 120Hz native momentum
      autoRaf: false, // Driven precisely by GSAP ticker
    });

    window.__lenis = lenis;

    // 2. Synchronize Lenis with GSAP's ScrollTrigger
    lenis.on('scroll', ScrollTrigger.update);

    const tickerCallback = (time: number) => {
      lenis.raf(time * 1000);
    };

    gsap.ticker.add(tickerCallback);
    // Crucial for GSAP + Lenis: lagSmoothing(0) prevents frame-drop micro-stutter
    gsap.ticker.lagSmoothing(0);

    // 3. Smooth internal anchor link navigation
    const handleAnchorClick = (e: MouseEvent) => {
      if (e.defaultPrevented || e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || !(e.target instanceof Element)) return;
      const target = (e.target as HTMLElement).closest('a[href^="#"]');
      if (target) {
        const href = target.getAttribute('href');
        if (href && href.length > 1) {
          let targetEl: HTMLElement | null = null;
          try { targetEl = document.getElementById(decodeURIComponent(href.slice(1))); } catch { return; }
          if (targetEl) {
            e.preventDefault();
            lenis.scrollTo(targetEl as HTMLElement, {
              offset: -70,
              duration: 0.9,
              easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            });
          }
        }
      }
    };

    document.addEventListener('click', handleAnchorClick, { passive: false });

    return () => {
      document.removeEventListener('click', handleAnchorClick);
      gsap.ticker.remove(tickerCallback);
      lenis.destroy();
      delete window.__lenis;
    };
  }, []);

  return <>{children}</>;
};
