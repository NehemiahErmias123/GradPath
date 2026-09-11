import { Link, useNavigate, useLocation } from "react-router-dom";

function Navbar() {
  const navigate = useNavigate();
  const location = useLocation();
  const token = localStorage.getItem("token");

  const handleLogout = () => {
    localStorage.removeItem("token");
    navigate("/");
  };

  if (!token) return null;

  const linkClass = (path) =>
    `font-sans text-[15px] font-medium transition-all duration-200 hover:scale-110 inline-block ${
      location.pathname === path ? "text-ink" : "text-muted hover:text-ink"
    }`;

  return (
    <nav className="px-8 py-4 grid grid-cols-3 items-center border-b border-hairline bg-paper/60 backdrop-blur-sm">
      <Link to="/dashboard" className="font-serif text-2xl justify-self-start">
        <span className="text-ink font-semibold">Grad</span>
        <span className="text-brass font-semibold">Path</span>
      </Link>

      <div className="flex gap-8 justify-self-center">
        <Link to="/dashboard" className={linkClass("/dashboard")}>
          Dashboard
        </Link>
        <Link to="/programs" className={linkClass("/programs")}>
          Programs
        </Link>
      </div>

      <button
        onClick={handleLogout}
        className="text-sm font-medium text-white bg-ink px-5 py-2 rounded-full transition-all duration-200 hover:scale-110 hover:bg-green justify-self-end"
      >
        Log out
      </button>
    </nav>
  );
}

export default Navbar;
