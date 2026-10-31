// NEXORA 3D assembly preview. Visual placement is illustrative, not CAD fitment.
import * as THREE from 'three';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

const BASE = '/Nexora/Assets/3D/builder/';
const MODELS = { case: 'case.glb', motherboard: 'motherboard.glb', cpu: 'cpu.glb', ram: 'ram.glb', gpu: 'gpu.glb', storage: 'ssd.glb', psu: 'psu.glb', cooler: 'cooler.glb' };
// Coordinates are approximate until actual case interior is calibrated.

const PLACEMENT = {
  motherboard: {
    position: [-0.35, 0.15, -0.40],
    size: 1.30
  },

  cpu: {
    position: [-0.35, 0.36, -0.06],
    size: 0.30
  },

  ram: {
    position: [0.24, 0.38, -0.02],
    size: 0.45
  },

  gpu: {
    position: [-0.18, -0.44, 0.25],
    size: 0.95
  },

  storage: {
    position: [0.22, -0.18, 0.15],
    size: 0.35
  },

  psu: {
    position: [0.15, -1.18, -0.10],
    size: 0.75
  },

  cooler: {
    position: [-0.34, 0.36, 0.27],
    size: 0.55
  }
};

const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
export async function createBuilder3D(element, onStatus = () => { }) {
  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(38, 1, .1, 100);
  camera.position.set(5.1, 2.8, 6.6);
  const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'high-performance' });
  renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
  renderer.outputColorSpace = THREE.SRGBColorSpace;
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  element.append(renderer.domElement);
  renderer.domElement.setAttribute('aria-label', 'Interactive 3D PC assembly preview');
  const controls = new OrbitControls(camera, renderer.domElement);
  controls.enableDamping = true; controls.enablePan = false; controls.minDistance = 3.8; controls.maxDistance = 13;
  controls.target.set(0, 0, 0);
  scene.add(new THREE.HemisphereLight(0xdcefff, 0x152238, 2.5));
  const light = new THREE.DirectionalLight(0xffffff, 3); light.position.set(3, 6, 5); scene.add(light);
  const accent = new THREE.PointLight(0x55dff7, 40, 10); accent.position.set(-3, 1, 2); scene.add(accent);
  const root = new THREE.Group(); scene.add(root);
  const loader = new GLTFLoader();
  const cached = new Map();
  const installed = new Map();
  let busy = false;
  function resize() { const w = element.clientWidth, h = element.clientHeight; if (!w || !h) return; camera.aspect = w / h; camera.updateProjectionMatrix(); renderer.setSize(w, h, false); }
  const observer = new ResizeObserver(resize); observer.observe(element); resize();
  let active = true;
  function draw() { if (!active) return; requestAnimationFrame(draw); controls.update(); renderer.render(scene, camera); }
  draw();
  function fetchModel(key) {
    if (!cached.has(key)) cached.set(key, new Promise((resolve, reject) => loader.load(BASE + MODELS[key], g => resolve(g.scene), undefined, reject)));
    return cached.get(key);
  }
  function normalize(model, maxSize) {
    model.updateMatrixWorld(true);
    const box = new THREE.Box3().setFromObject(model);
    const size = box.getSize(new THREE.Vector3());
    const largest = Math.max(size.x, size.y, size.z);
    if (!Number.isFinite(largest) || largest <= 0) throw new Error('Model geometry is empty');
    model.scale.multiplyScalar(maxSize / largest);
    model.updateMatrixWorld(true);
    const center = new THREE.Box3().setFromObject(model).getCenter(new THREE.Vector3());
    model.position.sub(center);
    return model;
  }
  async function install(key) {
    if (busy) return false;
    if (!MODELS[key]) throw new Error('Unknown part');
    busy = true;
    try {
      onStatus('Loading ' + MODELS[key] + '…');
      const source = await fetchModel(key);
      const model = normalize(source.clone(true), key === 'case' ? 3.65 : PLACEMENT[key].size);
      if (key === 'case') {
        const previous = installed.get('case'); if (previous) root.remove(previous);
        installed.set('case', model); root.add(model);
        onStatus('Case ready. Drag to rotate the 3D view.'); return true;
      }

      const target = new THREE.Vector3(...PLACEMENT[key].position);

      const holder = new THREE.Group();
      holder.add(model);
      root.add(holder);

      // Starting point outside the PC case
      const startPosition = new THREE.Vector3(-3.2, 1.3, 2.5);

      // Curved flight path towards the installation point
      const controlPoint = new THREE.Vector3(
        (startPosition.x + target.x) / 2,
        Math.max(startPosition.y, target.y) + 1.1,
        (startPosition.z + target.z) / 2 + 0.8
      );

      const curve = new THREE.QuadraticBezierCurve3(
        startPosition,
        controlPoint,
        target
      );

      // Initial position, rotation and size
      holder.position.copy(startPosition);
      holder.scale.setScalar(1.4);
      holder.rotation.set(-0.15, -0.5, 0.1);

      // Smooth acceleration and deceleration
      function smootherStep(t) {
        return t * t * t * (t * (t * 6 - 15) + 10);
      }

      // Longer duration for a more cinematic animation
      const duration = reducedMotion ? 0 : 1800;
      const begin = performance.now();

      await new Promise(resolve => {
        function animate(now) {
          const t = duration
            ? Math.min(1, (now - begin) / duration)
            : 1;

          const progress = smootherStep(t);

          // Follow a curved path
          holder.position.copy(curve.getPoint(progress));

          // Gradually shrink to the final size
          holder.scale.setScalar(
            THREE.MathUtils.lerp(1.4, 1, progress)
          );

          // Smooth rotation into the case
          holder.rotation.x = THREE.MathUtils.lerp(-0.15, 0, progress);
          holder.rotation.y = THREE.MathUtils.lerp(-0.5, 0, progress);
          holder.rotation.z = THREE.MathUtils.lerp(0.1, 0, progress);

          if (t < 1) {
            requestAnimationFrame(animate);
          } else {
            // Ensure exact final placement
            holder.position.copy(target);
            holder.scale.setScalar(1);
            holder.rotation.set(0, 0, 0);
            resolve();
          }
        }

        requestAnimationFrame(animate);
      });

      const previous = installed.get(key); if (previous) root.remove(previous);
      installed.set(key, holder); onStatus(key.toUpperCase() + ' visually installed.'); return true;
    } finally { busy = false; }
  }
  function remove(key) { const item = installed.get(key); if (item) { root.remove(item); installed.delete(key); } }
  function reset() { for (const key of [...installed.keys()]) if (key !== 'case') remove(key); }
  try { await install('case'); } catch (e) { onStatus('Could not load the case: ' + e.message); throw e; }
  return { install, remove, reset, dispose() { active = false; observer.disconnect(); controls.dispose(); renderer.dispose(); renderer.domElement.remove(); } };
}
