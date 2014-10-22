/*
 *
 * Sort tree structure using recursion and SQL cursor
 *
 * Boards have committees.
 * Committees have subcommittees.
 * Subcommittees have task forces.
 * Boards can also have task forces.
 * Etc.
 *
 * We want to define a "tree" with unlimited levels, based on only a pointer to a parent record.
 * Then we want to sort the results (we will later indent each level).
 *
 * Sorted keys will look like this:
 *
 * Acme Company Board
 * Acme Company Board|Budget Committee
 * Acme Company Board|Budget Committee|Tax Investment Subcommittee
 * Acme Company Board|Operations Committee
 * Acme Company Board|Planning Committee
 * Beta Inc Board
 * Etc.
 *
 */

/* define a temporary table to store keys for sorting */
create table #temp (
      ID smallint,
      SortString varchar(255)
);

/* define a cursor to iterate the database table */
declare c cursor
      for select CommitteeID, ParentID, CommitteeName from Committee WITH (NOLOCK)
            where (Active = '1' or '' = 'ALL');

/* define variables to store fields fetched from cursor */
declare @CommitteeID smallint;
declare @ParentID smallint;
declare @CommitteeName varchar(100);

/* define variables to process fetched record */
declare @SortString varchar(255) = '';
declare @tmpParentName varchar(100);
declare @tmpParentID smallint;

open c;
fetch next from c into @CommitteeID, @ParentID, @CommitteeName;
while @@fetch_status = 0
begin
      set @SortString = @CommitteeName;
      set @tmpParentID = @ParentID;
      /* recursive loop */
      while 1 = 1 begin
            /* get name of parent */
            set @tmpParentName = (select CommitteeName from Committee where CommitteeID = @tmpParentID);
            /* exit loop when reach top level of tree (value for pointer to parent is null/blank) */
            if @tmpParentName is null or @tmpParentName = '' break;
            /* build sort string by continually prepending name of parent */
            set @SortString = @tmpParentName + '|' + @SortString;
            /* set new parent id by looking at parent-of-parent */
            set @tmpParentID = (select ParentID from Committee where CommitteeID = @tmpParentID);
      end
      /* insert row into temporary table */
      insert into #temp values(@CommitteeID, @SortString);

      fetch next from c into @CommitteeID, @ParentID, @CommitteeName;
end
close c;
deallocate c;

/* retrieve database records, joining with temporary table and sorting by sort keys */
select
      c.CommitteeID,
      c.CommitteeName,
      c.Type,
      c.Active,
      (select top 1 DateMeetingStart from CommitteeMeeting m WITH (NOLOCK) WHERE
            m.CommitteeID = c.CommitteeID and m.DateMeetingStart <= GETDATE()
            order by DateMeetingStart desc) as LastMeeting,
      (select top 1 DateMeetingStart from CommitteeMeeting m WITH (NOLOCK) WHERE
            m.CommitteeID = c.CommitteeID and m.DateMeetingStart >= GETDATE()
            order by DateMeetingStart) as NextMeeting
      from #temp
      inner join Committee c WITH (NOLOCK) on c.CommitteeID = #temp.ID 
      order by #temp.SortString;

drop table #temp;
