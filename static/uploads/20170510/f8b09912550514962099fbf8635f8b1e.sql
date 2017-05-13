--select查询
select * from employees;

SELECT last_name, salary FROM employees;

SELECT e.*,d.department_name
  FROM employees e
      ,departments d
 WHERE e.department_id = d.department_id;
 
--表达式
SELECT last_name, salary, salary + 300, SALARY *2 FROM employees;
 

--关于NULL
SELECT e.last_name
      ,e.commission_pct
      ,e.commission_pct + 0.1
      ,e.commission_pct * 2
  FROM employees e;


--列别名
SELECT last_name as Name, commission_pct comm FROM employees;

SELECT last_name as "NamE", salary * 12  "Annual Salary" FROM employees;


--连接
SELECT last_name||job_id AS "Employees" FROM employees;

SELECT last_name || ' is a ' || job_id AS "Employee Details"
FROM employees;


--distinct 去重
select department_id from employees;

select distinct department_id from employees;


select last_name,department_id from employees;

select distinct last_name, department_id from employees;

--条件
select *
from employees e
where e.salary >=4400
and e.salary <=9000;



select *
from employees e
where e.salary between 9000 and 4400;


select *
from employees e
where e.department_id in (90,60);



select *
from employees e
where e.last_name like '_o%';



select *
from employees e
where e.last_name like 'k%';



--
SELECT * FROM EMPLOYees e
where e.department_id is null;

--

select *  from employees e
where e.salary > 10000
and e.job_id like '%MAN%';


select *  from employees e
where e.salary > 10000
or e.job_id like '%MAN%';



SELECT * FROM EMPLOYees e
where e.department_id is not null;



SELECT * FROM EMPLOYees e
where e.department_id not in (90);

