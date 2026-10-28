import React, { useCallback, useEffect, useState } from 'react';
import { SmoothScroll } from './components/SmoothScroll';
import { Header } from './components/Header';
import { HeroScrollPinned } from './components/HeroScrollPinned';
import { StatsBar } from './components/StatsBar';
import { HeaderBannersSection } from './components/HeaderBannersSection';
import { CatalogSection } from './components/CatalogSection';
import { LMSAndCRMSection } from './components/LMSAndCRMSection';
import { PathwaySection } from './components/PathwaySection';
import { WhyChooseConsultingSection } from './components/WhyChooseConsultingSection';
import { WelcomeSection } from './components/WelcomeSection';
import { StrategicTargetSection } from './components/StrategicTargetSection';
import { CEOMessageSection } from './components/CEOMessageSection';
import { LeadershipSection } from './components/LeadershipSection';
import { PhilosophySection } from './components/PhilosophySection';
import { WhyUsSection } from './components/WhyUsSection';
import { ProcedureSection } from './components/ProcedureSection';
import { SuccessStoriesSection } from './components/SuccessStoriesSection';
import { CampusLifeSection } from './components/CampusLifeSection';
import { AdmissionsFlightPlan } from './components/AdmissionsFlightPlan';
import { WaveMarquee } from './components/WaveMarquee';
import { NewsEventsSection } from './components/NewsEventsSection';
import { CallToAction } from './components/CallToAction';
import { Footer } from './components/Footer';
import { InquiryModal } from './components/InquiryModal';
import { PortalModal } from './components/PortalModal';
import { FloatingActions } from './components/FloatingActions';
import { getContent } from './content';

export const App: React.FC = () => {
  const [modalOpen, setModalOpen] = useState(false);
  const [portalOpen, setPortalOpen] = useState(false);
  const [event, setEvent] = useState('');
  const [portalRole, setPortalRole] = useState<'student' | 'parent' | 'crm' | 'faculty'>('student');

  const openInquiry = useCallback(() => {
    setEvent('');
    setModalOpen(true);
  }, []);
  const closeInquiry = useCallback(() => setModalOpen(false), []);
  const closePortal = useCallback(() => setPortalOpen(false), []);
  const openPortal = useCallback((role: 'student' | 'parent' | 'crm' | 'faculty' = 'student') => {
    setPortalRole(role);
    setPortalOpen(true);
  }, []);

  useEffect(() => {
    const openEvent = (e: Event) => {
      setEvent(String((e as CustomEvent).detail?.event || '').slice(0, 180));
      setModalOpen(true);
    };
    document.addEventListener('genz:inquiry', openEvent);
    return () => document.removeEventListener('genz:inquiry', openEvent);
  }, []);

  useEffect(() => {
    if (!modalOpen && !portalOpen) return;
    const previous = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
    window.__lenis?.stop();
    return () => {
      document.body.style.overflow = previous;
      window.__lenis?.start();
    };
  }, [modalOpen, portalOpen]);

  const sections = [
    { key: 'hero', node: <HeroScrollPinned onOpenModal={openInquiry} /> },
    { key: 'stats', node: <StatsBar /> },
    { key: 'headerBanners', node: <HeaderBannersSection onOpenModal={openInquiry} /> },
    { key: 'catalog', node: <CatalogSection /> },
    { key: 'platform', node: <LMSAndCRMSection onOpenPortalModal={openPortal} /> },
    { key: 'pathway', node: <PathwaySection onOpenModal={openInquiry} /> },
    { key: 'whyChooseConsulting', node: <WhyChooseConsultingSection onOpenModal={openInquiry} /> },
    { key: 'welcome', node: <WelcomeSection /> },
    { key: 'strategicTarget', node: <StrategicTargetSection onOpenModal={openInquiry} /> },
    { key: 'ceo', node: <CEOMessageSection onOpenConsultation={openInquiry} /> },
    { key: 'leadership', node: <LeadershipSection onOpenConsultation={openInquiry} /> },
    { key: 'philosophy', node: <PhilosophySection /> },
    { key: 'benefits', node: <WhyUsSection /> },
    { key: 'procedure', node: <ProcedureSection onOpenModal={openInquiry} /> },
    { key: 'stories', node: <SuccessStoriesSection /> },
    { key: 'campus', node: <CampusLifeSection onOpenModal={openInquiry} /> },
    { key: 'admissions', node: <AdmissionsFlightPlan onOpenModal={openInquiry} /> },
    { key: 'marquee', node: <WaveMarquee /> },
    { key: 'news', node: <NewsEventsSection onOpenModal={openInquiry} /> },
    { key: 'callToAction', node: <CallToAction onOpenModal={openInquiry} /> },
  ]
    .map((item, index) => ({
      ...item,
      order: Number(getContent(`sections.${item.key}.order`, index * 10)),
    }))
    .filter((item) => getContent(`sections.${item.key}.enabled`, true))
    .sort((a, b) => a.order - b.order);

  return (
    <SmoothScroll>
      <div className="min-h-screen bg-cream font-sans text-navy selection:bg-cyan selection:text-white">
        <Header onOpenModal={openInquiry} onOpenPortalModal={openPortal} />
        <main>
          {sections.map((item) => (
            <React.Fragment key={item.key}>{item.node}</React.Fragment>
          ))}
        </main>
        <Footer onOpenModal={openInquiry} />
        <InquiryModal isOpen={modalOpen} onClose={closeInquiry} event={event} />
        <PortalModal isOpen={portalOpen} onClose={closePortal} initialRole={portalRole} />
        <FloatingActions onOpenModal={openInquiry} />
      </div>
    </SmoothScroll>
  );
};

export default App;
