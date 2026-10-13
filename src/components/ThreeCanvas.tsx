import React, { useEffect, useRef } from 'react';
import * as THREE from 'three';

interface ThreeCanvasProps {
  scrollProgress: number; // 0 to 1
}

export const ThreeCanvas: React.FC<ThreeCanvasProps> = ({ scrollProgress }) => {
  const containerRef = useRef<HTMLDivElement>(null);
  const sceneRef = useRef<THREE.Scene | null>(null);
  const rendererRef = useRef<THREE.WebGLRenderer | null>(null);
  const globeGroupRef = useRef<THREE.Group | null>(null);
  const ringRef = useRef<THREE.Mesh | null>(null);
  const ring2Ref = useRef<THREE.Mesh | null>(null);
  const ring3Ref = useRef<THREE.Mesh | null>(null);
  const particlesRef = useRef<THREE.Points | null>(null);

  useEffect(() => {
    if (!containerRef.current) return;

    const width = containerRef.current.clientWidth || window.innerWidth;
    const height = containerRef.current.clientHeight || window.innerHeight;

    // 1. Scene
    const scene = new THREE.Scene();
    sceneRef.current = scene;

    // 2. Camera with adaptive perspective
    const camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
    camera.position.z = 8.5;

    // 3. Renderer with transparent background and crisp pixel ratio
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.domElement.style.width = '100%';
    renderer.domElement.style.height = '100%';
    renderer.domElement.style.display = 'block';
    containerRef.current.appendChild(renderer.domElement);
    rendererRef.current = renderer;

    // 4. Lighting
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.95);
    scene.add(ambientLight);

    const pointLight = new THREE.PointLight(0x0099ff, 3.5, 60);
    pointLight.position.set(6, 6, 6);
    scene.add(pointLight);

    const pointLight2 = new THREE.PointLight(0x38bdf8, 2.5, 60);
    pointLight2.position.set(-6, -6, -3);
    scene.add(pointLight2);

    // 5. Academic Globe / Orbital Group
    const globeGroup = new THREE.Group();
    scene.add(globeGroup);
    globeGroupRef.current = globeGroup;

    // Ethereal Holographic Latitude & Longitude Sphere (Clean, futuristic, non-intrusive)
    const sphereGeo = new THREE.SphereGeometry(2.35, 24, 18);
    const sphereMat = new THREE.MeshBasicMaterial({
      color: 0x38bdf8,
      wireframe: true,
      transparent: true,
      opacity: 0.22,
    });
    const sphere = new THREE.Mesh(sphereGeo, sphereMat);
    globeGroup.add(sphere);

    // Inner glowing sphere with soft cyan ambient radiance
    const innerGeo = new THREE.SphereGeometry(1.9, 32, 32);
    const innerMat = new THREE.MeshBasicMaterial({
      color: 0x0099ff,
      transparent: true,
      opacity: 0.08,
      wireframe: false,
    });
    const innerSphere = new THREE.Mesh(innerGeo, innerMat);
    globeGroup.add(innerSphere);

    // Primary Orbital Ring (Matching logo airplane flight path)
    const ringGeo = new THREE.TorusGeometry(3.3, 0.045, 16, 120);
    const ringMat = new THREE.MeshBasicMaterial({
      color: 0x00a8ff,
      transparent: true,
      opacity: 0.9,
    });
    const ring = new THREE.Mesh(ringGeo, ringMat);
    ring.rotation.x = Math.PI / 2.8;
    ring.rotation.y = Math.PI / 6;
    globeGroup.add(ring);
    ringRef.current = ring;

    // Secondary tilted ring
    const ringGeo2 = new THREE.TorusGeometry(3.8, 0.025, 16, 120);
    const ringMat2 = new THREE.MeshBasicMaterial({
      color: 0x38bdf8,
      transparent: true,
      opacity: 0.65,
    });
    const ring2 = new THREE.Mesh(ringGeo2, ringMat2);
    ring2.rotation.x = -Math.PI / 3.4;
    ring2.rotation.z = Math.PI / 4;
    globeGroup.add(ring2);
    ring2Ref.current = ring2;

    // Outer Expansive Orbital Ring (spans wide across the full header as requested)
    const ringGeo3 = new THREE.TorusGeometry(4.4, 0.018, 16, 140);
    const ringMat3 = new THREE.MeshBasicMaterial({
      color: 0x0099ff,
      transparent: true,
      opacity: 0.4,
    });
    const ring3 = new THREE.Mesh(ringGeo3, ringMat3);
    ring3.rotation.x = Math.PI / 2.2;
    ring3.rotation.y = -Math.PI / 5;
    globeGroup.add(ring3);
    ring3Ref.current = ring3;

    // Flight Particle (The Airplane beacon along the primary ring)
    const airplaneBeaconGeo = new THREE.ConeGeometry(0.2, 0.5, 8);
    const airplaneBeaconMat = new THREE.MeshStandardMaterial({
      color: 0x0099ff,
      emissive: 0x00a8ff,
      emissiveIntensity: 0.9,
    });
    const airplaneBeacon = new THREE.Mesh(airplaneBeaconGeo, airplaneBeaconMat);
    airplaneBeacon.position.set(3.3, 0, 0);
    airplaneBeacon.rotation.z = -Math.PI / 2;
    ring.add(airplaneBeacon);

    // Starfield / Knowledge Particles
    const particlesCount = 220;
    const posArray = new Float32Array(particlesCount * 3);
    for (let i = 0; i < particlesCount * 3; i++) {
      posArray[i] = (Math.random() - 0.5) * 16;
    }
    const particlesGeo = new THREE.BufferGeometry();
    particlesGeo.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
    const particlesMat = new THREE.PointsMaterial({
      size: 0.045,
      color: 0x0099ff,
      transparent: true,
      opacity: 0.65,
    });
    const particles = new THREE.Points(particlesGeo, particlesMat);
    scene.add(particles);
    particlesRef.current = particles;

    // Animation Loop
    let animationFrameId: number;
    const animate = () => {
      animationFrameId = requestAnimationFrame(animate);

      // Ambient continuous idle rotation
      if (globeGroupRef.current) {
        globeGroupRef.current.rotation.y += 0.003;
      }
      if (ringRef.current) {
        ringRef.current.rotation.z += 0.01;
      }
      if (ring2Ref.current) {
        ring2Ref.current.rotation.z -= 0.007;
      }
      if (ring3Ref.current) {
        ring3Ref.current.rotation.z += 0.005;
      }
      if (particlesRef.current) {
        particlesRef.current.rotation.y -= 0.0007;
      }

      renderer.render(scene, camera);
    };
    animate();

    // Resize Handler
    const handleResize = () => {
      if (!containerRef.current) return;
      const newWidth = containerRef.current.clientWidth || window.innerWidth;
      const newHeight = containerRef.current.clientHeight || window.innerHeight;
      camera.aspect = newWidth / newHeight;
      camera.updateProjectionMatrix();
      renderer.setSize(newWidth, newHeight);
    };
    window.addEventListener('resize', handleResize);

    return () => {
      window.removeEventListener('resize', handleResize);
      cancelAnimationFrame(animationFrameId);
      if (renderer.domElement && renderer.domElement.parentNode) {
        renderer.domElement.parentNode.removeChild(renderer.domElement);
      }
      renderer.dispose();
    };
  }, []);

  // Update 3D rotation and scale based on scroll progress
  useEffect(() => {
    if (!globeGroupRef.current) return;

    const targetRotY = scrollProgress * Math.PI * 4;
    const targetRotX = (scrollProgress - 0.5) * 0.7;
    const scale = 1 + Math.sin(scrollProgress * Math.PI) * 0.2;

    globeGroupRef.current.rotation.y = targetRotY;
    globeGroupRef.current.rotation.x = targetRotX;
    globeGroupRef.current.scale.set(scale, scale, scale);
  }, [scrollProgress]);

  return <div ref={containerRef} className="w-full h-full pointer-events-none" />;
};
